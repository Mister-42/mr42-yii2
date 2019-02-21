<?php
namespace app\models\articles;
use Yii;

class Search extends Articles {
	public $keyword;

	public function rules(): array {
		return [
			['keyword', 'required'],
			['keyword', 'string', 'length' => [3, 25]],
			['keyword', 'trim'],
		];
	}
}
