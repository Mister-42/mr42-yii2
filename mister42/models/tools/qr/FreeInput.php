<?php

namespace app\models\tools\qr;

use Yii;

class FreeInput extends \app\models\tools\Qr
{
    public $qrdata;

    public function attributeLabels(): array
    {
        $labels = parent::attributeLabels();

        $labels['qrdata'] = Yii::t('mr42', 'QR Data');
        return $labels;
    }

    public function generateQr(): bool
    {
        return parent::generate($this->qrdata);
    }

    public function rules(): array
    {
        $rules = parent::rules();

        $rules[] = ['qrdata', 'required'];
        $rules[] = ['qrdata', 'string'];
        return $rules;
    }
}
