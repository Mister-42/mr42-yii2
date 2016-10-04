<?php
namespace app\models\post;
use Yii;
use app\models\Pdf;
use dektrium\user\models\{Profile, User};
use yii\bootstrap\Html;
use yii\helpers\{StringHelper, Url};
use yii\web\AccessDeniedHttpException;

class Post extends \yii\db\ActiveRecord
{
	const STATUS_INACTIVE = 0;
	const STATUS_ACTIVE = 1;

	public static function tableName()
	{
		return '{{%article}}';
	}

	public function rules()
	{
		return [
			[['title', 'content'], 'required'],
			[['content'], 'string'],
			[['title', 'tags'], 'string', 'max' => 255],
			[['active'], 'boolean'],
		];
	}

	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'title' => 'Title',
			'url' => 'URL',
			'content' => 'Content',
			'tags' => 'Tags',
			'created' => 'Created At',
			'updated' => 'Updated At',
			'author' => 'User ID',
		];
	}

	public function addComment(Comment $comment)
	{
		$comment->parent = $this->id;
		$comment->user = (Yii::$app->user->isGuest) ? null : Yii::$app->user->id;
		$comment->active = (Yii::$app->user->isGuest) ? Self::STATUS_INACTIVE : Self::STATUS_ACTIVE;
		return $comment->save();
	}

	public function beforeDelete() {
		if (!parent::beforeDelete())
			return false;

		if (!$this->belongsToViewer())
			return false;

		return true;
	}

	public function afterFind()
	{
		$this->url = $this->url ?? $this->title;
	}

	public function beforeSave($insert)
	{
		if (Yii::$app->user->isGuest)
			throw new AccessDeniedHttpException('Please login.');

		if (!parent::beforeSave($insert))
			return false;

		$datetime = time();
		$this->url = $this->url ?? null;

		if ($insert) {
			$this->author = Yii::$app->user->id;
			$this->created = $datetime;
		} elseif (!$this->belongsToViewer())
			return false;

		$this->updated = $datetime;

		return true;
	}

	public function belongsToViewer()
	{
		if (Yii::$app->user->isGuest)
			return false;

		return $this->author == Yii::$app->user->id;
	}

	public function buildPdf($model, $html)
	{
		$user = new Profile();
		$profile = $user->find($model->user->id)->one();
		$name = (empty($profile->name) ? Html::encode($model->user->username) : Html::encode($profile->name));
		$tags = Yii::t('site', '{results, plural, =1{1 tag} other{# tags}}', ['results' => count(StringHelper::explode($model->tags))]);

		$pdf = new Pdf();
		return $pdf->create(
			'@runtime/PDF/posts/'.sprintf('%05d', $model->id),
			$html,
			$model->updated,
			[
				'author' => $name,
				'created' => $model->created,
				'footer' => $tags.': '.$model->tags.'|Author: '.$name.'|Page {PAGENO} of {nb}',
				'header' => Html::a(Yii::$app->name, Url::to(['site/index'], true)).'|'.Html::a($model->title, Url::to(['post/index', 'id' => $model->id], true)).'|' . date('D, j M Y', $model->updated),
				'keywords' => $model->tags,
				'subject' => $model->title,
				'title' => implode(' ∷ ', [$model->title, Yii::$app->name]),
			]
		);
	}

	public function findNewerOne()
	{
		return static::find()
				->where('id > :id', [':id' => $this->id])
				->orderBy('id asc')
				->one();
	}

	public function findOlderOne()
	{
		return static::find()
				->where('id < :id', [':id' => $this->id])
				->orderBy('id desc')
				->one();
	}

	public function getComments()
	{
		return $this->hasMany(Comment::className(), ['parent' => 'id']);
	}

	public function getNewerLink()
	{
		if (!$model = $this->findNewerOne())
			return null;

		return Html::a('Next Article &raquo;', ['post/index', 'id' => $model->id, 'title' => $model->url], ['class' => 'btn btn-sm btn-default pull-right', 'data-toggle' => 'tooltip', 'data-placement' => 'left', 'title' => Html::encode($model->title)]);
	}

	public function getOlderLink()
	{
		if (!$model = $this->findOlderOne())
			return null;

		return Html::a('&laquo; Previous Article', ['post/index', 'id' => $model->id, 'title' => $model->url], ['class' => 'btn btn-sm btn-default pull-left', 'data-toggle' => 'tooltip', 'data-placement' => 'right', 'title' => Html::encode($model->title)]);
	}

	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'author']);
	}
}
