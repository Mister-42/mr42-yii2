<?php
namespace app\models\articles;
use Yii;
use himiklab\yii2\recaptcha\ReCaptchaValidator;
use yii\behaviors\TimestampBehavior;
use yii\bootstrap4\Html;
use yii\db\ActiveRecord;
use yii\web\AccessDeniedHttpException;

class Comments extends ActiveRecord {
	public $nameField;
	public $emailField;
	public $captcha;

	public static function tableName(): string {
		return '{{%articles_comments}}';
	}

	public function rules(): array {
		$rules = [
			[['title', 'content'], 'required'],
			[['nameField', 'emailField', 'website', 'title', 'content'], 'trim'],
			'charCount' => [['content'], 'string', 'max' => 4096],
			['nameField', 'string', 'max' => 25],
			['emailField', 'string', 'max' => 50],
			['website', 'string', 'max' => 128],
			[['emailField', 'website'], 'default', 'value' => null],
			['emailField', 'email', 'checkDNS' => true, 'enableIDN' => true],
			['website', 'url', 'defaultScheme' => 'http', 'enableIDN' => true],
		];

		if (Yii::$app->user->isGuest) :
			$rules[] = ['captcha', ReCaptchaValidator::className()];
			$rules[] = [['nameField', 'emailField'], 'required'];
		endif;
		return $rules;
	}

	public function attributeLabels(): array {
		return [
			'nameField' => Yii::t('mr42', 'Name'),
			'emailField' => Yii::t('mr42', 'Email Address'),
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

	public function afterFind(): void {
		parent::afterFind();
		$this->content = Yii::$app->formatter->cleanInput($this->content, 'gfm-comment');
	}

	public function beforeSave($insert): bool {
		if (!parent::beforeSave($insert))
			return false;

		if (!$insert && Yii::$app->user->isGuest)
			throw new AccessDeniedHttpException('Please login.');

		$this->content = Yii::$app->formatter->cleanInput($this->content, false);
		$this->name = $this->nameField ?? $this->name;
		$this->email = $this->emailField ?? $this->email;
		return true;
	}

	public function showApprovalButton(): string {
		return Html::a(
			$this->active ? Yii::$app->icon->show('thumbs-down', ['class' => 'mr-1']).Yii::t('mr42', 'Renounce') : Yii::$app->icon->show('thumbs-up', ['class' => 'mr-1']).Yii::t('mr42', 'Approve'),
			['commentstatus', 'id' => $this->id, 'action' => 'toggleapproval'],
			['class' => $this->active ? 'badge badge-warning ml-1' : 'badge badge-success ml-1']
		);
	}

	public function sendCommentMail($model, $comment): void {
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

	public static function find(): Query {
		return new Query(get_called_class());
	}
}
