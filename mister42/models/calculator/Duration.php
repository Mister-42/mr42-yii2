<?php
namespace app\models\calculator;
use DateTime;
use Yii;

class Duration extends \yii\base\Model {
	public $fromDate;
	public $toDate;

	public function rules(): array {
		return [
			[['fromDate', 'toDate'], 'date', 'format' => 'php:Y-m-d'],
		];
	}

	public function attributeLabels(): array {
		return [
			'fromDate' => 'Start Date',
			'toDate' => 'End Date',
		];
	}

	public function duration(): bool {
		if ($this->validate()) {
			$this->fromDate = empty($this->fromDate) ? date('Y-m-d') : $this->fromDate;
			$this->toDate = empty($this->toDate) ? date('Y-m-d') : $this->toDate;
			$diff = (new DateTime($this->fromDate))->diff(new DateTime($this->toDate));
			Yii::$app->getSession()->setFlash('duration-success', $diff);
			return true;
		}
		return false;
	}
}