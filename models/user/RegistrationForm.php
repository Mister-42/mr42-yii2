<?php
namespace app\models\user;
use dektrium\user\models\RegistrationForm as BaseRegistrationForm;

class RegistrationForm extends BaseRegistrationForm
{
	public $captcha;

	public function attributeLabels()
	{
		$labels = parent::attributeLabels();
		$labels['captcha'] = 'Completely Automated Public Turing test to tell Computers and Humans Apart';
		return $labels;
	}

	public function rules()
	{
		$rules = parent::rules();
		$rules[] = ['captcha', 'required'];
		$rules[] = ['captcha', 'captcha', 'captchaAction'=> 'tech/captcha'];
		return $rules;
	}
}
