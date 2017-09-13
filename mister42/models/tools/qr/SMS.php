<?php
namespace app\models\tools\qr;
use Yii;

class SMS extends Phone {
	public function generateQr(): bool {
		return parent::generate("SMS:{$this->phone}");
	}
}
