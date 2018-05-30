<?php
namespace app\models\articles;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\bootstrap4\Html;
use yii\db\ActiveRecord;
use yii\web\AccessDeniedHttpException;

class Comments extends ActiveRecord {
	public $nameField;
	public $email;
	public $captcha;

	const STATUS_INACTIVE = false;
	const STATUS_ACTIVE = true;

	public static function tableName(): string {
		return '{{%articles_comments}}';
	}

	public function rules(): array {
		$rules = [
			[['title', 'content'], 'required'],
			[['nameField', 'email', 'website', 'title', 'content'], 'trim'],
			'charCount' => [['content'], 'string', 'max' => 4096],
			['nameField', 'string', 'max' => 25],
			['email', 'string', 'max' => 50],
			['website', 'string', 'max' => 128],
			['email', 'email', 'checkDNS' => true, 'enableIDN' => true],
			['website', 'url', 'defaultScheme' => 'http', 'enableIDN' => true],
		];

		if (Yii::$app->user->isGuest) :
			$rules[] = [['captcha'], 'captcha'];
			$rules[] = [['name', 'email', 'captcha'], 'required'];
		endif;
		return $rules;
	}

	public function attributeLabels(): array {
		return [
			'nameField' => Yii::t('mr42', 'Name'),
			'email' => Yii::t('mr42', 'Email Address'),
			'website' => Yii::t('mr42', 'Website URL'),
			'title' => Yii::t('mr42', 'Subject'),
			'content' => Yii::t('mr42', 'Comment'),
			'captcha' => Yii::t('mr42', 'CAPTCHA'),
		];
	}

	public function behaviors(): array {
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

	public function beforeSave($insert): bool {
		if (!parent::beforeSave($insert)) :
			return false;
		endif;

		if (!$insert && Yii::$app->user->isGuest) :
			throw new AccessDeniedHttpException('Please login.');
		endif;

		$this->content = Yii::$app->formatter->cleanInput($this->content, false);
		$this->name = $this->nameField ?? null;
		$this->email = $this->email ?? null;
		$this->website = $this->website ?? null;
		return true;
	}

	public function showApprovalButton(): string {
		return Html::a(
			$this->active ? Yii::$app->icon->show('thumbs-down', ['class' => 'mr-1']).Yii::t('mr42', 'Renounce') : Yii::$app->icon->show('thumbs-up', ['class' => 'mr-1']).Yii::t('mr42', 'Approve'),
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
			->setSubject('A new comment has been posted on "'.$model->title.'"')
			->send();

		if (Yii::$app->user->isGuest) :
			Yii::$app->mailer->compose(
					['html' => 'commentToCommenter'],
					['model' => $model, 'comment' => $comment]
				)
				->setTo([$comment->email => $comment->name])
				->setFrom([Yii::$app->params['secrets']['params']['noreplyEmail'] => Yii::$app->name])
				->setSubject('Thank you for your reply on "'.$model->title.'"')
				->send();
		endif;
	}

	public function getArticle() {
		return $this->hasOne(Articles::class, ['id' => 'parent']);
	}

	public static function find() {
		return parent::find()
			->onCondition(
				php_sapi_name() === 'cli' || (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin)
					? ['or', ['active' => [Self::STATUS_INACTIVE, Self::STATUS_ACTIVE]]]
					: ['active' => Self::STATUS_ACTIVE]
			);
	}
}
