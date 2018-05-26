<?php
namespace app\models\tools\qr;
use Yii;

class Geographic extends \app\models\tools\Qr {
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

		$labels['lat'] = Yii::t('mr42', 'Latitude');
		$labels['lng'] = Yii::t('mr42', 'Longitude');
		$labels['altitude'] = Yii::t('mr42', 'Altitude');
		return $labels;
	}

	public function generateQr(): bool {
		$data[] = $this->getDataOrOmit('', $this->lat);
		$data[] = $this->getDataOrOmit('', $this->lng);
		$data[] = $this->getDataOrOmit('', $this->altitude);
		return parent::generate('GEO:'.implode(',', array_filter($data)));
	}
}
