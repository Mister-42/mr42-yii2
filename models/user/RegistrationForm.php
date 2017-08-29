<?php
namespace app\models\user;

class RegistrationForm extends \Da\User\Form\RegistrationForm {
	public $captcha;

	public function attributeLabels() {
		$labels = parent::attributeLabels();
		$labels['captcha'] = 'Verification Code';
		return $labels;
	}

	public function rules() {
		$rules = parent::rules();
		$rules[] = ['captcha', 'required'];
		$rules[] = ['captcha', 'captcha'];
		return $rules;
	}
}
