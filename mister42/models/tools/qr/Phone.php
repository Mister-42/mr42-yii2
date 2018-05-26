<?php
namespace app\models\tools\qr;
use Yii;

class Phone extends \app\models\tools\Qr {
	public $phone;

	public function rules(): array {
		$rules = parent::rules();

		$rules[] = ['phone', 'required'];
		$rules[] = ['phone', 'string'];
		return $rules;
	}

	public function attributeLabels(): array {
		$labels = parent::attributeLabels();

		$labels['phone'] = Yii::t('mr42', 'Telephone Number');
		return $labels;
	}

	public function generateQr(): bool {
		return parent::generate("TEL:{$this->phone}");
	}
}
