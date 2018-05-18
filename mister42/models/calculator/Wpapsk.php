<?php
namespace app\models\calculator;

class Wpapsk extends \yii\base\Model {
	public $ssid;
	public $password;
	public $psk;

	public function rules(): array {
		return [
			[['ssid', 'password'], 'required'],
			['ssid', 'string', 'max' => 32],
			['password', 'string', 'min' => 8, 'max' => 63],
		];
	}

	public function attributeLabels(): array {
		return [
			'ssid' => 'SSID',
			'password' => 'WPA Passphrase',
			'psk' => 'Pre-Shared Key',
		];
	}
}
