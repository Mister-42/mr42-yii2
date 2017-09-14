<?php
namespace app\models\tools\qr;
use Yii;

class FreeInput extends \app\models\tools\Qr {
	public $qrdata;

	public function rules(): array {
		$rules = parent::rules();

		$rules[] = ['qrdata', 'required'];
		$rules[] = ['qrdata', 'string'];
		return $rules;
	}

	public function generateQr(): bool {
		return parent::generate($this->qrdata);
	}
}
