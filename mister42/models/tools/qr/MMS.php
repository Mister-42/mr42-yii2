<?php
namespace app\models\tools\qr;
use Yii;

class MMS extends \app\models\tools\Qr {
	public $phone;
	public $message;

	public function rules(): array {
		$rules = parent::rules();

		$rules[] = [['phone', 'message'], 'required'];
		$rules[] = [['phone', 'message'], 'string'];
		return $rules;
	}

	public function attributeLabels(): array {
		$labels = parent::attributeLabels();

		$labels['phone'] = 'Telephone Number';
		return $labels;
	}

	public function generateQr(): bool {
		return parent::generate("MMSTO:{$this->phone}:{$this->message}");
	}
}