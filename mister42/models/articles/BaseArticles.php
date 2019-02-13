<?php
namespace app\models\articles;
use Yii;
use app\models\Pdf;
use Da\User\Model\{Profile, User};
use yii\behaviors\TimestampBehavior;
use yii\bootstrap4\Html;
use yii\helpers\{StringHelper, Url};
use yii\web\AccessDeniedHttpException;

class BaseArticles extends \yii\db\ActiveRecord {
	public static function tableName(): string {
		return '{{%articles}}';
	}

	public function rules(): array {
		return [
			[['title', 'content'], 'required'],
			['content', 'string'],
			[['title', 'url', 'tags'], 'string', 'max' => 255],
			['source', 'url', 'enableIDN' => true],
			[['pdf', 'active'], 'boolean'],
		];
	}

	public function attributeLabels(): array {
		return [
			'title' => Yii::t('mr42', 'Title'),
			'content' => Yii::t('mr42', 'Content'),
			'url' => Yii::t('mr42', 'URL'),
			'source' => Yii::t('mr42', 'Source URL'),
			'tags' => Yii::t('mr42', 'Tags'),
			'pdf' => Yii::t('mr42', 'Create PDF'),
		];
	}

	public function behaviors(): array{
		return [
			[
				'class' => TimestampBehavior::class,
				'createdAtAttribute' => 'created',
				'updatedAtAttribute' => 'updated',
			],
		];
	}

	public function addComment(Comments $comment): bool {
		$comment->parent = $this->id;
		$comment->user = Yii::$app->user->isGuest ? null : Yii::$app->user->id;
		$comment->active = Yii::$app->user->isGuest ? false : true;
		return $comment->save();
	}

	public function beforeDelete(): bool {
		return (parent::beforeDelete() && $this->belongsToViewer()) ? true : false;
	}

	public function beforeSave($insert): bool {
		if (Yii::$app->user->isGuest)
			throw new AccessDeniedHttpException('Please login.');

		if (!parent::beforeSave($insert))
			return false;

		$this->url = !empty($this->url) ? $this->url : null;
		$this->source = !empty($this->source) ? $this->source : null;

		if ($insert) :
			$this->author = Yii::$app->user->id;
		elseif (!$this->belongsToViewer()) :
			return false;
		endif;

		return true;
	}

	public function belongsToViewer(): bool {
		return $this->author === Yii::$app->user->id;
	}

	public static function buildPdf($model, string $html) {
		$user = new Profile();
		$profile = $user->find($model->user->id)->one();
		$name = empty($profile->name) ? $model->user->username : $profile->name;
		$tags = Yii::t('mr42', '{results, plural, =1{1 tag} other{# tags}}', ['results' => count(StringHelper::explode($model->tags))]);

		$pdf = new Pdf();
		return $pdf->create(
			'@runtime/PDF/articles/'.sprintf('%05d', $model->id),
			$html,
			$model->updated,
			[
				'author' => $name,
				'created' => $model->created,
				'footer' => $tags.': '.$model->tags.'|Author: '.$name.'|Page {PAGENO} of {nb}',
				'header' => Html::a(Yii::$app->name, Url::to(['site/index'], true)).'|'.Html::a($model->title, ['/permalink/articles', 'id' => $model->id]).'|'.date('D, j M Y', $model->updated),
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

	public static function find(): Query {
		return new Query(get_called_class());
	}
}
