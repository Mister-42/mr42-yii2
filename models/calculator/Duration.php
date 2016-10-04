<?php
namespace app\models\calculator;
use DateTime;
use Yii;

class Duration extends \yii\base\Model
{
	public $from;
	public $to;

	public function rules()
	{
		return [
			[['from', 'to'], 'date', 'format' => 'php:Y-m-d'],
		];
	}

	public function attributeLabels()
	{
		return [
			'from' => 'Start Date',
			'to' => 'End Date',
		];
	}

	public function duration()
	{
		if ($this->validate()) {
			$this->from = ($this->from) ? $this->from : date('Y-m-d');
			$this->to = ($this->to) ? $this->to : date('Y-m-d');
			$diff = (new DateTime($this->from))->diff(new DateTime($this->to));
			Yii::$app->getSession()->setFlash('duration-success', $diff);
			return true;
		}

		return false;
	}
}
