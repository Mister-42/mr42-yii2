<?php
namespace app\models\articles;
use Yii;

class Search extends \yii\base\Model {
	public $keyword;

	public function init() {
		parent::init();
		$this->keyword = Yii::$app->request->get('q');
	}

	public function rules(): array {
		return [
			['keyword', 'required'],
			['keyword', 'trim'],
			['keyword', 'string', 'length' => [3, 25]],
		];
	}
}
