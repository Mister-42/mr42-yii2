<?php

namespace mister42\models\tools\qr;

use Yii;

class WiFi extends \mister42\models\tools\Qr
{
    public $authentication;
    public $hidden;
    public $password;
    public $ssid;

    public function attributeLabels(): array
    {
        $labels = parent::attributeLabels();

        $labels['authentication'] = Yii::t('mr42', 'Authentication');
        $labels['ssid'] = Yii::t('mr42', 'SSID');
        $labels['password'] = Yii::t('mr42', 'Password');
        $labels['hidden'] = Yii::t('mr42', 'Hidden Network');
        return $labels;
    }

    public function generateQr(): bool
    {
        $data = [];
        $this->addData($data, 'T:', $this->authentication === 'none' ? null : $this->authentication);
        $this->addData($data, 'S:', $this->ssid);
        $password = ctype_xdigit($this->password) ? $this->password : "\"{$this->password}\"";
        $this->addData($data, 'P:', $this->authentication === 'none' ? null : $password);
        $this->addData($data, 'H:', $this->hidden ? 'true' : null);
        return parent::generate('WIFI:' . implode(';', $data) . ';');
    }

    public function rules(): array
    {
        $rules = parent::rules();

        $rules[] = [['authentication', 'ssid'], 'required'];
        $rules[] = ['password', 'required', 'when' => function () {
            return $this->authentication !== 'none';
        }, 'whenClient' => "function(attribute,value){return $('#qr-authentication').val()!='none';}"];
        $rules[] = ['authentication', 'in', 'range' => parent::getWifiAuthentication(true)];
        $rules[] = ['ssid', 'string', 'max' => 32];
        $rules[] = ['password', 'string', 'min' => 8, 'max' => 63];
        $rules[] = ['hidden', 'boolean'];
        return $rules;
    }
}
