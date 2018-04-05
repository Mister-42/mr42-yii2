<?php
namespace app\models\articles;
use Yii;
use app\models\Pdf;
use Da\User\Model\{Profile, User};
use yii\behaviors\TimestampBehavior;
use yii\bootstrap\Html;
use yii\helpers\{StringHelper, Url};
use yii\web\AccessDeniedHttpException;

class BaseArticles extends \yii\db\ActiveRecord {
	const STATUS_INACTIVE = 0;
	const STATUS_ACTIVE = 1;

	public static function tableName() {
		return '{{%articles}}';
	}

	public function rules() {
		return [
			[['title', 'content'], 'required'],
			['content', 'string'],
			[['title', 'url', 'tags'], 'string', 'max' => 255],
			['source', 'url', 'enableIDN' => true],
			[['pdf', 'active'], 'boolean'],
		];
	}

	public function attributeLabels() {
		return [
			'url' => 'URL',
			'source' => 'Source URL',
			'pdf' => 'Create PDF',
		];
	}

	public function behaviors() {
		return [
			[
				'class' => TimestampBehavior::class,
				'createdAtAttribute' => 'created',
				'updatedAtAttribute' => 'updated',
			],
		];
	}

	public function addComment(Comments $comment) {
		$comment->parent = $this->id;
		$comment->user = Yii::$app->user->isGuest ? null : Yii::$app->user->id;
		$comment->active = Yii::$app->user->isGuest ? Self::STATUS_INACTIVE : Self::STATUS_ACTIVE;
		return $comment->save();
	}

	public function beforeDelete() {
		return (parent::beforeDelete() && $this->belongsToViewer()) ? true : false;
	}

	public function beforeSave($insert) {
		if (Yii::$app->user->isGuest)
			throw new AccessDeniedHttpException('Please login.');

		if (!parent::beforeSave($insert))
			return false;

		$this->url = $this->url ?? null;
		$this->source = $this->source ?? null;

		if ($insert)
			$this->author = Yii::$app->user->id;
		elseif (!$this->belongsToViewer())
			return false;

		return true;
	}

	public function belongsToViewer() {
		return Yii::$app->user->isGuest ? false : $this->author === Yii::$app->user->id;
	}

	public function buildPdf($model, $html) {
		$user = new Profile();
		$profile = $user->find($model->user->id)->one();
		$name = empty($profile->name) ? Html::encode($model->user->username) : Html::encode($profile->name);
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
				'header' => Html::a(Yii::$app->name, Url::to(['site/index'], true)).'|'.Html::a($model->title, "https://mr42.me/art{$model->id}").'|' . date('D, j M Y', $model->updated),
				'keywords' => $model->tags,
				'subject' => $model->title,
				'title' => $model->title,
			]
		);
	}

	public function getUser() {
		return $this->hasOne(User::class, ['id' => 'author']);
	}

	public function getComments() {
		return $this->hasMany(Comments::class, ['parent' => 'id']);
	}

	public static function find() {
		return parent::find()
			->onCondition(
				php_sapi_name() === 'cli' || (Yii::$app->user->identity->isAdmin && Yii::$app->controller->action->id !== 'sitemap')
					? ['or', ['active' => [Self::STATUS_INACTIVE, Self::STATUS_ACTIVE]]]
					: ['active' => Self::STATUS_ACTIVE]
			);
	}
}
