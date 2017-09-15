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
		$data['amount'] = $this->amount;
		$data['label'] = $this->name;
		$data['message'] = $this->message;
		$query = http_build_query(array_filter($data));
		return parent::generate("bitcoin:{$this->address}?{$query}");
	}
}
