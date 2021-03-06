<?php

namespace mister42\models\tools\qr;

use Yii;

class YouTube extends \mister42\models\tools\Qr
{
    public $id;

    public function attributeLabels(): array
    {
        $labels = parent::attributeLabels();

        $labels['id'] = Yii::t('mr42', 'YouTube Video ID');
        return $labels;
    }

    public function generateQr(): bool
    {
        return parent::generate("youtube://{$this->id}");
    }

    public function rules(): array
    {
        $rules = parent::rules();

        $rules[] = ['id', 'required'];
        $rules[] = ['id', 'string'];
        return $rules;
    }
}
