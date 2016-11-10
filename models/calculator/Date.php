<?php
namespace app\models\calculator;
use DateTime;
use Yii;

class Date extends \yii\base\Model {
	public $from;
	public $days;

	public function rules() {
		return [
			[['from'], 'date', 'format' => 'php:Y-m-d'],
			[['days'], 'number'],
		];
	}

	public function attributeLabels() {
		return [
			'from' => 'Start Date',
			'days' => 'Days to Add',
		];
	}

	public function diff() {
		if ($this->validate()) {
			$this->from = $this->from ?? date('Y-m-d');
			$this->days = $this->days ?? 42;
			$date = new DateTime($this->from);
			$date->modify($this->days . ' days');
			Yii::$app->getSession()->setFlash('date-success', $date);
			return true;
		}
		return false;
	}
}
