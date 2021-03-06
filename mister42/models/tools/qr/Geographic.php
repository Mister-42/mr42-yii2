<?php

namespace mister42\models\tools\qr;

use Yii;

class Geographic extends \mister42\models\tools\Qr
{
    public $altitude;
    public $lat;
    public $lng;

    public function attributeLabels(): array
    {
        $labels = parent::attributeLabels();

        $labels['lat'] = Yii::t('mr42', 'Latitude');
        $labels['lng'] = Yii::t('mr42', 'Longitude');
        $labels['altitude'] = Yii::t('mr42', 'Altitude');
        return $labels;
    }

    public function generateQr(): bool
    {
        $data = [];
        $this->addData($data, '', $this->lat);
        $this->addData($data, '', $this->lng);
        $this->addData($data, '', $this->altitude);
        return parent::generate('GEO:' . implode(',', $data));
    }

    public function rules(): array
    {
        $rules = parent::rules();

        $rules[] = ['lat', 'number', 'min' => -90, 'max' => 90];
        $rules[] = ['lng', 'number', 'min' => -180, 'max' => 180];
        $rules[] = ['altitude', 'number'];
        $rules[] = [['lat', 'lng'], 'required'];
        return $rules;
    }
}
