<?php
namespace app\models\calculator;
use DateTime;
use Yii;

class Date extends \yii\base\Model {
	public $from;
	public $days;

	public function rules(): array {
		return [
			[['from'], 'date', 'format' => 'php:Y-m-d'],
			[['days'], 'number'],
		];
	}

	public function attributeLabels(): array {
		return [
			'from' => 'Start Date',
			'days' => 'Days to Add',
		];
	}

	public function diff(): bool {
		if ($this->validate()) {
			$this->from = empty($this->from) ? date('Y-m-d') : $this->from;
			$this->days = empty($this->days) ? 42 : $this->days;
			$date = new DateTime($this->from);
			$date->modify($this->days . ' days');
			Yii::$app->getSession()->setFlash('date-success', $date);
			return true;
		}
		return false;
	}
}
