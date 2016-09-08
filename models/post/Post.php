<?php
namespace app\models\post;
use Yii;
use dektrium\user\models\User;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\web\AccessDeniedHttpException;

class Post extends ActiveRecord
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
			'content' => 'Content',
			'tags' => 'Tags',
			'created' => 'Created At',
			'updated' => 'Updated At',
			'author' => 'User ID',
		];
	}

	public function beforeSave($insert)
	{
		if (Yii::$app->user->isGuest)
			throw new AccessDeniedHttpException('Please login.');

		if (!parent::beforeSave($insert))
			return false;

		$datetime = time();

		if ($insert) {
			$this->author = Yii::$app->user->id;
			$this->created = $datetime;
		} elseif (!$this->belongsToViewer())
			return false;

		$this->updated = $datetime;

		return true;
	}

	public function beforeDelete() {
		if (!parent::beforeDelete()) {
			return false;
		}

		if (!$this->belongsToViewer())
			return false;

		return true;
	}

	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'author']);
	}

	public function getComments()
	{
		return $this->hasMany(Comment::className(), ['parent' => 'id']);
	}

	public function belongsToViewer()
	{
		if (Yii::$app->user->isGuest)
			return false;

		return $this->author == Yii::$app->user->id;
	}

	public function findOlderOne()
	{
		return static::find()
				->where('id < :id', [':id' => $this->id])
				->orderBy('id desc')
				->one();
	}

	public function findNewerOne()
	{
		return static::find()
				->where('id > :id', [':id' => $this->id])
				->orderBy('id asc')
				->one();
	}

	public function getNewerLink()
	{
		if (!$model = $this->findNewerOne())
			return null;

		return Html::a('Next Article', ['post/index', 'id' => $model->id, 'title' => $model->title], ['title' => Html::encode($model->title), 'data-toggle' => 'tooltip', 'data-placement' => 'left']) . ' &raquo;';
	}

	public function getOlderLink()
	{
		if (!$model = $this->findOlderOne())
			return null;

		return '&laquo; ' . Html::a('Previous Article', ['post/index', 'id' => $model->id, 'title' => $model->title], ['title' => Html::encode($model->title), 'data-toggle' => 'tooltip', 'data-placement' => 'right']);
	}

	public function addComment(Comment $comment)
	{
		$comment->parent = $this->id;
		$comment->user = (Yii::$app->user->isGuest) ? null : Yii::$app->user->id;
		$comment->active = (Yii::$app->user->isGuest) ? Self::STATUS_INACTIVE : Self::STATUS_ACTIVE;
		return $comment->save();
	}
}
