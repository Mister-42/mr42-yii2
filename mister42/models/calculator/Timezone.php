<?php
namespace app\models\calculator;
use DateTime;
use DateTimeZone;
use Yii;

class Timezone extends \yii\base\Model {
	public $source;
	public $datetime;
	public $target;

	public function rules(): array {
		return [
			[['source', 'target'], 'required'],
			['datetime', 'date', 'format' => 'php:Y-m-d H:i'],
			[['source', 'target'], 'in', 'range' => self::getTimezones(false)],
			['datetime', 'default', 'value' => date('Y-m-d')],
		];
	}

	public function attributeLabels(): array {
		return [
			'source' => 'Source Time Zone',
			'datetime' => 'Date & Time in Source Time Zone',
			'target' => 'Target Time Zone',
		];
	}

	public function diff(): bool {
		if (!$this->validate())
			return false;

		$time = new DateTime($this->datetime, new DateTimeZone($this->source));
		$time->setTimezone(new DateTimeZone($this->target));
		Yii::$app->getSession()->setFlash('timezone-success', $time);
		return true;
	}

	public function getTimezones($replace): array {
		foreach (DateTimeZone::listIdentifiers() as $timezone)
			$timezones[$timezone] = $replace ? str_replace('_', ' ', $timezone) : $timezone;
		return $timezones;
	}
}
