<?php
namespace app\models\user;
use Yii;

class RegistrationForm extends \dektrium\user\models\RegistrationForm {
	public $captcha;

	public function attributeLabels() {
		$labels = parent::attributeLabels();
		$labels['captcha'] = 'Completely Automated Public Turing test to tell Computers and Humans Apart';
		return $labels;
	}

	public function rules() {
		$rules = parent::rules();
		$rules[] = ['captcha', 'required'];
		$rules[] = ['captcha', 'captcha'];
		return $rules;
	}
}
