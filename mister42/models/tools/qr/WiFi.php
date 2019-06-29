<?php

namespace app\models\tools\qr;

use Yii;

class WiFi extends \app\models\tools\Qr
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
        $data[] = $this->authentication !== 'none' ? "T:{$this->authentication}" : null;
        $data[] = "S:{$this->ssid}";
        $password = ctype_xdigit($this->password) ? $this->password : "\"{$this->password}\"";
        $data[] = $this->authentication !== 'none' ? "P:{$password}" : null;
        $data[] = $this->hidden ? 'H:true' : null;
        return parent::generate('WIFI:' . implode(';', array_filter($data)) . ';');
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
