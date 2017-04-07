<?php
namespace app\models\site;
use Yii;

class Contact extends \yii\base\Model
{
	public $name;
	public $email;
	public $title;
	public $content;
	public $attachment;
	public $captcha;

	public function rules(): array {
		$rules = [
			[['name', 'email', 'title', 'content'], 'required'],
			[['name', 'email', 'title', 'content'], 'trim'],
			'charCount' => [['content'], 'string', 'max' => 8192],
			[['name'], 'string', 'max' => 25],
			[['email'], 'string', 'max' => 50],
			[['email'], 'email', 'checkDNS' => true, 'enableIDN' => true],
			[['attachment'], 'file',
				'minSize' => 64,
				'maxSize' => 1024 * 1024 * 5,
			],
			[['captcha'], 'captcha'],
		];

		if (!Yii::$app->request->post())
			$rules[] = [['captcha'], 'required'];
		return $rules;
	}

	public function attributeLabels(): array {
		return [
			'email' => 'Email Address',
			'title' => 'Subject',
			'content' => 'Message',
			'captcha' => 'Completely Automated Public Turing test to tell Computers and Humans Apart',
		];
	}

	public function contact(): bool {
		if ($this->validate()) {
			$mailer = Yii::$app->mailer->compose()
				->setTo(Yii::$app->params['adminEmail'])
				->setFrom([$this->email => $this->name])
				->setSubject($this->title)
				->setTextBody($this->content);

			if ($this->attachment)
				$mailer->attach($this->attachment->tempName, ['fileName' => $this->attachment->name]);

			return $mailer->send();
		}
		return false;
	}
}
