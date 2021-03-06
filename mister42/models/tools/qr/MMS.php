<?php

namespace mister42\models\tools\qr;

use Yii;

class MMS extends \mister42\models\tools\Qr
{
    public $message;
    public $phone;

    public function attributeLabels(): array
    {
        $labels = parent::attributeLabels();

        $labels['phone'] = Yii::t('mr42', 'Telephone Number');
        $labels['message'] = Yii::t('mr42', 'Message');
        return $labels;
    }

    public function generateQr(): bool
    {
        return parent::generate("MMSTO:{$this->phone}:{$this->message}");
    }

    public function rules(): array
    {
        $rules = parent::rules();

        $rules[] = [['phone', 'message'], 'required'];
        $rules[] = [['phone', 'message'], 'string'];
        return $rules;
    }
}
