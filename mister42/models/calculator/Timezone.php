<?php

namespace mister42\models\calculator;

use DateTime;
use DateTimeZone;
use Yii;
use yii\helpers\ArrayHelper;

class Timezone extends \yii\base\Model
{
    public $datetime;
    public $source = 'Europe/Berlin';
    public $target = 'Europe/Moscow';

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

    public function getTimezones(): array
    {
        foreach (DateTimeZone::listIdentifiers() as $timezone) {
            $name = str_replace('_', ' ', $timezone);
            $date = new DateTime('now', new DateTimeZone($timezone));

            $timezones[] = ['name' => "{$name} (UTC{$date->format('P')})", 'offset' => $date->getOffset(), 'zone' => $timezone];
            ArrayHelper::multisort($timezones, 'offset', SORT_ASC, SORT_NUMERIC);
        }
        return ArrayHelper::map($timezones, 'zone', 'name');
    }

    public function rules(): array
    {
        return [
            [['source', 'target'], 'required'],
            ['datetime', 'date', 'format' => 'php:Y-m-d H:i'],
            [['source', 'target'], 'in', 'range' => DateTimeZone::listIdentifiers()],
            ['datetime', 'default', 'value' => date('Y-m-d H:i')],
        ];
    }
}
