<?php
namespace app\models\site;
use Yii;

class Contact extends \yii\base\Model {
	public $name;
	public $email;
	public $title;
	public $content;
	public $attachment;
	public $captcha;

	public function rules(): array {
		return [
			[['name', 'email', 'title', 'content', 'captcha'], 'required'],
			[['name', 'email', 'title', 'content'], 'trim'],
			'charCount' => ['content', 'string', 'max' => 8192],
			['name', 'string', 'max' => 25],
			['email', 'string', 'max' => 50],
			['email', 'email', 'checkDNS' => true, 'enableIDN' => true],
			['attachment', 'file', 'minSize' => 64, 'maxSize' => 1024 * 1024 * 5],
			['captcha', 'captcha'],
		];
	}

	public function attributeLabels(): array {
		return [
			'name' => Yii::t('mr42', 'Name'),
			'email' => Yii::t('mr42', 'Email Address'),
			'title' => Yii::t('mr42', 'Subject'),
			'content' => Yii::t('mr42', 'Message'),
			'attachment' => Yii::t('mr42', 'Attachment'),
			'captcha' => Yii::t('mr42', 'CAPTCHA'),
		];
	}

	public function contact(): bool {
		if (!$this->validate())
			return false;

		$mailer = Yii::$app->mailer->compose()
			->setTo(Yii::$app->params['secrets']['params']['adminEmail'])
			->setFrom([$this->email => $this->name])
			->setSubject(Yii::$app->name.' - '.$this->title)
			->setTextBody($this->content);

		if ($this->attachment)
			$mailer->attach($this->attachment->tempName, ['fileName' => $this->attachment->name]);

		return $mailer->send();
	}
}
