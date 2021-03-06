<?php

namespace mister42\models\calculator;

use DateTime;
use Yii;

class Date extends \yii\base\Model
{
    public $days;
    public $from;

    public function attributeLabels(): array
    {
        return [
            'from' => Yii::t('mr42', 'Start Date'),
            'days' => Yii::t('mr42', 'Days to Add'),
        ];
    }

    public function calculate(): bool
    {
        if (!$this->validate()) {
            return false;
        }
        $date = new DateTime($this->from);
        $date->modify($this->days . ' days');
        Yii::$app->getSession()->setFlash('date-success', $date);
        return true;
    }

    public function rules(): array
    {
        return [
            ['from', 'date', 'format' => 'php:Y-m-d'],
            ['days', 'number'],
            ['from', 'default', 'value' => date('Y-m-d')],
            ['days', 'default', 'value' => 42],
        ];
    }
}
