<?php
namespace app\models\tools\qr;
use Yii;

class Geographic extends \app\models\tools\Qr {
	public $address;
	public $lat;
	public $lng;
	public $altitude;

	public function rules(): array {
		$rules = parent::rules();

		$rules[] = ['lat', 'number', 'min' => -90, 'max' => 90];
		$rules[] = ['lng', 'number', 'min' => -180, 'max' => 180];
		$rules[] = ['altitude', 'number'];
		$rules[] = [['lat', 'lng'], 'required'];
		return $rules;
	}

	public function attributeLabels(): array {
		$labels = parent::attributeLabels();

		$labels['lat'] = 'Latitude';
		$labels['lng'] = 'Longitude';
		return $labels;
	}

	public function generateQr(): bool {
		return parent::generate("GEO:{$this->lat},{$this->lng},{$this->altitude}");
	}
}
