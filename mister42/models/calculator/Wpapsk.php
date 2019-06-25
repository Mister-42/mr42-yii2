<?php

namespace app\models\calculator;

use Yii;

class Wpapsk extends \yii\base\Model
{
    public $ssid;
    public $password;
    public $psk;

    public function rules(): array
    {
        return [
            [['ssid', 'password'], 'required'],
            ['ssid', 'string', 'max' => 32],
            ['password', 'string', 'min' => 8, 'max' => 63],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'ssid' => Yii::t('mr42', 'SSID'),
            'password' => Yii::t('mr42', 'WPA Passphrase'),
            'psk' => Yii::t('mr42', 'Pre-Shared Key'),
        ];
    }
}
