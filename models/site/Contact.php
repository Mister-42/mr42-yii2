<?php
namespace app\models\site;
use Yii;
use yii\base\Model;

class Contact extends Model
{
	public $name;
	public $email;
	public $title;
	public $content;
	public $captcha;

	public function rules()
	{
		$rules = [
			[['name', 'email', 'title', 'content'], 'required'],
			[['name', 'email', 'title', 'content'], 'trim'],
			'charCount' => [['content'], 'string', 'max' => 8192],
			[['name'], 'string', 'max' => 25],
			[['email'], 'string', 'max' => 50],
			[['email'], 'email', 'checkDNS' => true, 'enableIDN' => true],
			[['captcha'], 'captcha'],
		];

		if (!Yii::$app->request->post()) {
			$rules[] = [['captcha'], 'required'];
		}

		return $rules;
	}

	public function attributeLabels()
	{
		return [
			'name' => 'Name',
			'email' => 'Email Address',
			'title' => 'Subject',
			'content' => 'Message',
			'captcha' => 'Completely Automated Public Turing test to tell Computers and Humans Apart',
		];
	}

	public function contact()
	{
		if ($this->validate()) {
			return Yii::$app->mailer->compose()
				->setTo(Yii::$app->params['adminEmail'])
				->setFrom([$this->email => $this->name])
				->setSubject(Yii::$app->name . ' message ∷ ' . $this->title)
				->setTextBody($this->content)
				->send();
		}
		return false;
	}
}
