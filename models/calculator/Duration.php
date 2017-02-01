<?php
namespace app\models\calculator;
use DateTime;
use Yii;

class Duration extends \yii\base\Model {
	public $fromDate;
	public $toDate;

	public function rules() {
		return [
			[['fromDate', 'toDate'], 'date', 'format' => 'php:Y-m-d'],
		];
	}

	public function attributeLabels() {
		return [
			'fromDate' => 'Start Date',
			'toDate' => 'End Date',
		];
	}

	public function duration() {
		if ($this->validate()) {
			$this->fromDate = $this->fromDate ?? date('Y-m-d');
			$this->toDate = $this->toDate ?? date('Y-m-d');
			$diff = (new DateTime($this->fromDate))->diff(new DateTime($this->toDate));
			Yii::$app->getSession()->setFlash('duration-success', $diff);
			return true;
		}
		return false;
	}
}
