<?php

namespace app\models\user;

use Da\User\Validator\ReCaptchaValidator;

class RegistrationForm extends \Da\User\Form\RegistrationForm
{
    public $captcha;

    public function attributeLabels(): array
    {
        $labels = parent::attributeLabels();
        $labels['captcha'] = 'CAPTCHA';
        return $labels;
    }

    public function rules(): array
    {
        $rules = parent::rules();
        $rules[] = [['captcha'], 'safe'];
        $rules[] = ['captcha', ReCaptchaValidator::class];
        return $rules;
    }
}
