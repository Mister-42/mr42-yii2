<?php
namespace app\models\tools\qr;
use Yii;

class WiFi extends \app\models\tools\Qr {
	public $authentication;
	public $ssid;
	public $password;
	public $hidden;

	public function rules(): array {
		$rules = parent::rules();

		$rules[] = [['authentication', 'ssid'], 'required'];
		$rules[] = ['password', 'required', 'when' => function() {
						return $this->authentication !== 'none';
					}, 'whenClient' => "function(attribute,value){return $('#qr-authentication').val()!='none';}"];
		$rules[] = ['authentication', 'in', 'range' => parent::getWifiAuthentication(true)];
		$rules[] = ['ssid', 'string', 'max' => 32];
		$rules[] = ['password', 'string', 'min' => 8, 'max' => 63];
		$rules[] = ['hidden', 'boolean'];
		return $rules;
	}

	public function attributeLabels(): array {
		$labels = parent::attributeLabels();

		$labels['ssid'] = 'SSID';
		$labels['hidden'] = 'Hidden Network';
		return $labels;
	}

	public function generateQr(): bool {
		$data[] = $this->authentication !== 'none' ? "T:{$this->authentication}" : null;
		$data[] = "S:{$this->ssid}";
		$password = ctype_xdigit($this->password) ? $this->password : "\"{$this->password}\"";
		$data[] = $this->authentication !== 'none' ? "P:{$password}" : null;
		$data[] = $this->hidden ? "H:true" : null;
		return parent::generate("WIFI:".implode(';', array_filter($data)).';');
	}
}
