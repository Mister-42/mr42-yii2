<?php
namespace app\models\articles;
use Yii;
use app\models\user\User;
use himiklab\yii2\recaptcha\ReCaptchaValidator;
use yii\behaviors\TimestampBehavior;
use yii\bootstrap4\Html;

class ArticlesComments extends \yii\db\ActiveRecord {
	public $captcha;
	public $parsedContent;

	public static function tableName(): string {
		return '{{%articles_comments}}';
	}

	public function rules(): array {
		$rules = [
			[['parent', 'title', 'content'], 'required'],
			[['parent', 'created', 'user', 'active'], 'integer'],
			['content', 'string'],
			'charCount' => ['content', 'string', 'max' => 4096],
			[['title', 'website'], 'string', 'max' => 128],
			['name', 'string', 'max' => 25],
			['email', 'string', 'max' => 50],
			[['name', 'email', 'website'], 'default', 'value' => null],
			['active', 'default', 'value' => 0],
			['user', 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user' => 'id']],
			['parent', 'exist', 'skipOnError' => false, 'targetClass' => Articles::className(), 'targetAttribute' => ['parent' => 'id']],
		];

		if (Yii::$app->user->isGuest) :
			$rules[] = ['captcha', ReCaptchaValidator::className()];
			$rules[] = [['name', 'email'], 'required'];
		endif;
		return $rules;
	}

	public function attributeLabels(): array {
		return [
			'id' => Yii::t('mr42', 'ID'),
			'parent' => Yii::t('mr42', 'Parent'),
			'title' => Yii::t('mr42', 'Title'),
			'content' => Yii::t('mr42', 'Content'),
			'created' => Yii::t('mr42', 'Created'),
			'user' => Yii::t('mr42', 'User'),
			'name' => Yii::t('mr42', 'Name'),
			'email' => Yii::t('mr42', 'Email'),
			'website' => Yii::t('mr42', 'Website'),
			'active' => Yii::t('mr42', 'Active'),
		];
	}

	public function behaviors(): array{
		return [
			[
				'class' => TimestampBehavior::class,
				'createdAtAttribute' => 'created',
				'updatedAtAttribute' => null,
			],
		];
	}

	public function afterFind(): void {
		parent::afterFind();
		$this->parsedContent = Yii::$app->formatter->cleanInput($this->content, 'gfm-comment');
	}

	public function saveComment(self $comment): bool {
		$comment->user = Yii::$app->user->isGuest ? null : Yii::$app->user->id;
		$comment->active = Yii::$app->user->isGuest ? 0 : 1;
		return $comment->save();
	}

	public function showApprovalButton(): string {
		return Html::a(
			$this->active
				? Yii::$app->icon->show('thumbs-down', ['class' => 'mr-1']).Yii::t('mr42', 'Renounce')
				: Yii::$app->icon->show('thumbs-up', ['class' => 'mr-1']).Yii::t('mr42', 'Approve'),
			['togglecomment', 'id' => $this->id],
			['class' => $this->active ? 'btn btn-sm btn-outline-warning ml-1' : 'btn btn-sm btn-outline-success ml-1']
		);
	}

	public function sendCommentMail(Articles $model, self $comment): void {
		Yii::$app->mailer->compose(
				['text' => 'commentToAuthor'],
				['model' => $model, 'comment' => $comment]
			)
			->setTo([$model->author->email => $model->author->username])
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

	public function getAuthor() {
		return $this->hasOne(User::className(), ['id' => 'user']);
	}

	public function getArticle() {
		return $this->hasOne(Articles::className(), ['id' => 'parent']);
	}

	public static function find() {
		return new Query(get_called_class());
	}
}
