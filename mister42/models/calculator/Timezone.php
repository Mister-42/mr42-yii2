<?php

namespace app\models\calculator;

use DateTime;
use DateTimeZone;
use Yii;

class Timezone extends \yii\base\Model
{
    public $datetime;
    public $source;
    public $target;

    public function attributeLabels(): array
    {
        return [
            'source' => Yii::t('mr42', 'Source Time Zone'),
            'datetime' => Yii::t('mr42', 'Date & Time in Source Time Zone'),
            'target' => Yii::t('mr42', 'Target Time Zone'),
        ];
    }

    public function calculate(): bool
    {
        if (!$this->validate()) {
            return false;
        }
        $time = new DateTime($this->datetime, new DateTimeZone($this->source));
        $time->setTimezone(new DateTimeZone($this->target));
        Yii::$app->getSession()->setFlash('timezone-success', $time);
        return true;
    }

    public function getTimezones(bool $replace = true): array
    {
        foreach (DateTimeZone::listIdentifiers() as $timezone) {
            $timezones[$timezone] = $replace ? str_replace('_', ' ', $timezone) : $timezone;
        }
        return $timezones;
    }

    public function rules(): array
    {
        return [
            [['source', 'target'], 'required'],
            ['datetime', 'date', 'format' => 'php:Y-m-d H:i'],
            [['source', 'target'], 'in', 'range' => self::getTimezones(false)],
            ['datetime', 'default', 'value' => date('Y-m-d H:i')],
        ];
    }
}
