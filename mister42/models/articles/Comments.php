<?php
namespace app\models\articles;
use Yii;
use app\models\Icon;
use yii\behaviors\TimestampBehavior;
use yii\bootstrap4\Html;
use yii\db\ActiveRecord;
use yii\web\AccessDeniedHttpException;

class Comments extends ActiveRecord {
	public $captcha;

	const STATUS_INACTIVE = false;
	const STATUS_ACTIVE = true;

	public static function tableName() {
		return '{{%articles_comments}}';
	}

	public function rules() {
		$rules = [
			[['title', 'content'], 'required'],
			[['name', 'email', 'website', 'title', 'content'], 'trim'],
			'charCount' => [['content'], 'string', 'max' => 4096],
			[['name'], 'string', 'max' => 25],
			[['email'], 'string', 'max' => 50],
			[['website'], 'string', 'max' => 128],
			[['email'], 'email', 'checkDNS' => true, 'enableIDN' => true],
			[['website'], 'url', 'defaultScheme' => 'http', 'enableIDN' => true],
		];

		if (Yii::$app->user->isGuest) {
			$rules[] = [['captcha'], 'captcha'];
			$rules[] = [['name', 'email', 'captcha'], 'required'];
		}
		return $rules;
	}

	public function attributeLabels() {
		return [
			'content' => 'Comment',
			'email' => 'Email Address',
			'website' => 'Website URL',
			'captcha' => 'CAPTCHA',
		];
	}

	public function behaviors() {
		return [
			[
				'class' => TimestampBehavior::class,
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['created'],
				],
			],
		];
	}

	public function afterFind() {
		parent::afterFind();
		$this->content = Yii::$app->formatter->cleanInput($this->content, 'gfm-comment');
	}

	public function beforeSave($insert) {
		if (!parent::beforeSave($insert))
			return false;

		if (!$insert && Yii::$app->user->isGuest)
			throw new AccessDeniedHttpException('Please login.');

		$this->content = Yii::$app->formatter->cleanInput($this->content, false);
		$this->name = $this->name ?? null;
		$this->email = $this->email ?? null;
		$this->website = $this->website ?? null;
		return true;
	}

	public function showApprovalButton() {
		return Html::a(
			$this->active ? Icon::show('thumbs-down').' Renounce' : Icon::show('thumbs-up').' Approve',
			['commentstatus', 'id' => $this->id, 'action' => 'toggleapproval'],
			['class' => $this->active ? 'badge badge-warning ml-1' : 'badge badge-success ml-1']
		);
	}

	public function sendCommentMail($model, $comment) {
		Yii::$app->mailer->compose(
				['text' => 'commentToAuthor'],
				['model' => $model, 'comment' => $comment]
			)
			->setTo([$model->user->email => $model->user->username])
			->setFrom([Yii::$app->params['secrets']['params']['noreplyEmail'] => Yii::$app->name])
			->setSubject('A new comment has been posted on "' . $model->title . '"')
			->send();

		if (Yii::$app->user->isGuest) {
			Yii::$app->mailer->compose(
					['html' => 'commentToCommenter'],
					['model' => $model, 'comment' => $comment]
				)
				->setTo([$comment->email => $comment->name])
				->setFrom([Yii::$app->params['secrets']['params']['noreplyEmail'] => Yii::$app->name])
				->setSubject('Thank you for your reply on "' . $model->title . '"')
				->send();
		}
	}

	public function getArticle() {
		return $this->hasOne(Articles::class, ['id' => 'parent']);
	}

	public static function find() {
		return parent::find()
			->onCondition(
				php_sapi_name() !== 'cli' && Yii::$app->user->isGuest && !Yii::$app->user->identity->isAdmin
					? ['active' => Self::STATUS_ACTIVE]
					: ['or', ['active' => [Self::STATUS_INACTIVE, Self::STATUS_ACTIVE]]]
			);
	}
}
