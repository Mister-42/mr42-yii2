<?php
namespace app\models\articles;
use Yii;
use app\models\Pdf;
use dektrium\user\models\{Profile, User};
use yii\bootstrap\Html;
use yii\helpers\{StringHelper, Url};
use yii\web\AccessDeniedHttpException;

class Articles extends \yii\db\ActiveRecord {
	const STATUS_INACTIVE = 0;
	const STATUS_ACTIVE = 1;

	public static function tableName() {
		return '{{%articles}}';
	}

	public function rules() {
		return [
			[['title', 'content'], 'required'],
			[['content'], 'string'],
			[['title', 'tags'], 'string', 'max' => 255],
			[['active'], 'boolean'],
		];
	}

	public function attributeLabels() {
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

	public function addComment(Comments $comment) {
		$comment->parent = $this->id;
		$comment->user = (Yii::$app->user->isGuest) ? null : Yii::$app->user->id;
		$comment->active = (Yii::$app->user->isGuest) ? Self::STATUS_INACTIVE : Self::STATUS_ACTIVE;
		return $comment->save();
	}

	public function beforeDelete() {
		if (!parent::beforeDelete() || !$this->belongsToViewer())
			return false;
		return true;
	}

	public function afterFind() {
		parent::afterFind();
		$this->url = $this->url ?? $this->title;
	}

	public function beforeSave($insert) {
		if (Yii::$app->user->isGuest)
			throw new AccessDeniedHttpException('Please login.');

		if (!parent::beforeSave($insert))
			return false;

		$datetime = time();
		$this->url = $this->url ?? null;
		$this->updated = $datetime;

		if ($insert) {
			$this->author = Yii::$app->user->id;
			$this->created = $datetime;
		} elseif (!$this->belongsToViewer())
			return false;

		return true;
	}

	public function belongsToViewer() {
		if (Yii::$app->user->isGuest)
			return false;
		return $this->author == Yii::$app->user->id;
	}

	public function buildPdf($model, $html) {
		$user = new Profile();
		$profile = $user->find($model->user->id)->one();
		$name = (empty($profile->name) ? Html::encode($model->user->username) : Html::encode($profile->name));
		$tags = Yii::t('site', '{results, plural, =1{1 tag} other{# tags}}', ['results' => count(StringHelper::explode($model->tags))]);

		$pdf = new Pdf();
		return $pdf->create(
			'@runtime/PDF/articles/'.sprintf('%05d', $model->id),
			$html,
			$model->updated,
			[
				'author' => $name,
				'created' => $model->created,
				'footer' => $tags.': '.$model->tags.'|Author: '.$name.'|Page {PAGENO} of {nb}',
				'header' => Html::a(Yii::$app->name, Url::to(['site/index'], true)).'|'.Html::a($model->title, Url::to(['articles/index', 'id' => $model->id], true)).'|' . date('D, j M Y', $model->updated),
				'keywords' => $model->tags,
				'subject' => $model->title,
				'title' => implode(' ∷ ', [$model->title, Yii::$app->name]),
			]
		);
	}

	public function getComments() {
		return $this->hasMany(Comments::className(), ['parent' => 'id']);
	}

	public function getNewerLink() {
		$model = static::find()
			->where('id > :id', [':id' => $this->id])
			->orderBy('id asc')
			->one();

		if (!$model)
			return null;
		return Html::a('Next Article &raquo;', ['articles/index', 'id' => $model->id, 'title' => $model->url], ['class' => 'btn btn-sm btn-default pull-right', 'data-toggle' => 'tooltip', 'data-placement' => 'left', 'title' => Html::encode($model->title)]);
	}

	public function getOlderLink() {
		$model = static::find()
			->where('id < :id', [':id' => $this->id])
			->orderBy('id desc')
			->one();

		if (!$model)
			return null;
		return Html::a('&laquo; Previous Article', ['articles/index', 'id' => $model->id, 'title' => $model->url], ['class' => 'btn btn-sm btn-default pull-left', 'data-toggle' => 'tooltip', 'data-placement' => 'right', 'title' => Html::encode($model->title)]);
	}

	public function getUser() {
		return $this->hasOne(User::className(), ['id' => 'author']);
	}

	public static function find() {
		return parent::find()
			->onCondition(
				php_sapi_name() !== 'cli' && Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin
					? ['active' => Self::STATUS_ACTIVE]
					: ['or', ['active' => [Self::STATUS_INACTIVE, Self::STATUS_ACTIVE]]]
			);
	}
}
