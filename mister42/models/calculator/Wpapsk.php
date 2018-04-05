<?php
namespace app\models\calculator;

class Wpapsk extends \yii\base\Model {
	public $ssid;
	public $pass;

	public function rules(): array {
		return [
			[['ssid', 'pass'], 'required'],
			['ssid', 'string', 'max'=>32],
			['pass', 'string', 'min'=>8, 'max'=>63],
		];
	}

	public function attributeLabels(): array {
		return [
			'ssid' => 'SSID',
			'pass' => 'WPA Passphrase',
		];
	}
}
