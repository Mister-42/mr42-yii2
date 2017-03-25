<?php
namespace app\models\calculator;
use DateTime;
use DateTimeZone;
use Yii;

class Timezone extends \yii\base\Model {
	public $source;
	public $datetime;
	public $target;

	public function rules() {
		return [
			[['source', 'datetime', 'target'], 'required'],
			[['datetime'], 'date', 'format' => 'php:Y-m-d H:i'],
			[['source', 'target'], 'in', 'range' => self::getTimezones(false)],
		];
	}

	public function attributeLabels() {
		return [
			'source' => 'Source Time Zone',
			'datetime' => 'Date & Time',
			'target' => 'Target Time Zone',
		];
	}

	public function diff() {
		if ($this->validate()) {
			$time = new DateTime($this->datetime, new DateTimeZone($this->source));
			$time->setTimezone(new DateTimeZone($this->target));
			Yii::$app->getSession()->setFlash('timezone-success', $time);
			return true;
		}
		return false;
	}

	public function getTimezones($replace) {
		foreach (DateTimeZone::listIdentifiers() as $timezone)
			$timezones[$timezone] = $replace ? str_replace('_', ' ', $timezone) : $timezone;
		return $timezones;
	}
}
