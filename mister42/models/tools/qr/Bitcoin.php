<?php
namespace app\models\tools\qr;
use Yii;

class Bitcoin extends \app\models\tools\Qr {
	public $address;
	public $amount;
	public $name;
	public $message;

	public function rules(): array {
		$rules = parent::rules();

		$rules[] = [['address', 'amount'], 'required'];
		$rules[] = [['address', 'name', 'message'], 'string'];
		return $rules;
	}

	public function generateQr(): bool {
		$query = http_build_query(['amount' => $this->amount, 'label' => $this->name, 'message' => $this->message]);
		return parent::generate("bitcoin:{$this->address}?{$query}");
	}
}
