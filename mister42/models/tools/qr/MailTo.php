<?php

namespace mister42\models\tools\qr;

use Yii;

class MailTo extends \mister42\models\tools\Qr
{
    public $email;

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

    public function rules(): array
    {
        $rules = parent::rules();

        $rules[] = ['email', 'required'];
        $rules[] = [['email'], 'email', 'enableIDN' => true];
        return $rules;
    }
}
