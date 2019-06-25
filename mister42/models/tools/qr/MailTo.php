<?php

namespace app\models\tools\qr;

use Yii;

class MailTo extends \app\models\tools\Qr
{
    public $email;

    public function rules(): array
    {
        $rules = parent::rules();

        $rules[] = ['email', 'required'];
        $rules[] = [['email'], 'email', 'enableIDN' => true];
        return $rules;
    }

    public function attributeLabels(): array
    {
        $labels = parent::attributeLabels();

        $labels['email'] = Yii::t('mr42', 'Email Address');
        return $labels;
    }

    public function generateQr(): bool
    {
        return parent::generate("MAILTO:{$this->email}");
    }
}
