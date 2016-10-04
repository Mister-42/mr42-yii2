<?php
namespace app\models\post;
use Yii;
use app\models\General;
use yii\bootstrap\Html;
use yii\web\AccessDeniedHttpException;

class Comment extends \yii\db\ActiveRecord
{
	public $captcha;

	const STATUS_INACTIVE = '0';
	const STATUS_ACTIVE = '1';

	public static function tableName()
	{
		return '{{%article_comment}}';
	}

	public function rules()
	{
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
			$rules[] = [['name', 'email'], 'required'];
			$rules[] = ['captcha', 'required'];
		}

		return $rules;
	}

	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'parent' => 'Article ID',
			'title' => 'Comment Title',
			'content' => 'Comment',
			'created' => 'Created At',
			'user' => 'User',
			'name' => 'Name',
			'email' => 'Email Address',
			'website' => 'Website URL',
			'captcha' => 'Completely Automated Public Turing test to tell Computers and Humans Apart',
		];
	}

	public function beforeSave($insert) {
		if (!parent::beforeSave($insert))
			return false;

		if ($insert) {
			$this->created = time();
		}

		if (!$insert && Yii::$app->user->isGuest) {
			throw new AccessDeniedHttpException('Please login.');
		}

		$this->content = General::cleanInput($this->content, false);
		$this->name = ($this->name) ? $this->name : null;
		$this->email = ($this->email) ? $this->email : null;
		$this->website = ($this->website) ? $this->website : null;

		return true;
	}

	public function showApprovalButton()
	{
		return Html::a(
				($this->active) ? Html::icon('thumbs-down').' Renounce' : Html::icon('thumbs-up').' Approve',
				['commentstatus', 'id' => $this->id, 'action' => 'toggleapproval'],
				['class' => ($this->active) ? 'btn btn-xs btn-warning' : 'btn btn-xs btn-success', 'style' => 'margin-top:25px;']
			);
	}

	public function sendCommentMail($model, $comment)
	{
		Yii::$app->mailer->compose(
				['text' => 'commentToAuthor'],
				['model' => $model, 'comment' => $comment]
			)
			->setTo([$model->user->email => $model->user->username])
			->setFrom([Yii::$app->params['noreplyEmail'] => Yii::$app->name])
			->setSubject('A new comment has been posted on "' . $model->title . '"')
			->send();

		if (Yii::$app->user->isGuest) {
			Yii::$app->mailer->compose(
					['html' => 'commentToCommenter'],
					['model' => $model, 'comment' => $comment]
				)
				->setTo([$comment->email => $comment->name])
				->setFrom([Yii::$app->params['noreplyEmail'] => Yii::$app->name])
				->setSubject('Thank you for your reply on "' . $model->title . '"')
				->send();
		}
	}

	public static function find()
	{
		return parent::find()
			->where(Yii::$app->user->isGuest ? ['active' => Self::STATUS_ACTIVE] : ['or', ['active' => [Self::STATUS_INACTIVE, Self::STATUS_ACTIVE]]])
		;
	}
}
