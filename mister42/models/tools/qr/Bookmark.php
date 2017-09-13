<?php
namespace app\models\tools\qr;
use Yii;

class Bookmark extends \app\models\tools\Qr {
	public $title;
	public $url;

	public function rules(): array {
		$rules = parent::rules();

		$rules[] = ['url', 'required'];
		$rules[] = [['title'], 'string'];
		$rules[] = [['url'], 'url', 'defaultScheme' => 'http', 'enableIDN' => true];
		return $rules;
	}

	public function attributeLabels(): array {
		$labels = parent::attributeLabels();

		$labels['url'] = 'URL';
		return $labels;
	}

	public function generateQr(): bool {
		return parent::generate("MEBKM:TITLE:{$this->title};URL:{$this->url};;");
	}
}
