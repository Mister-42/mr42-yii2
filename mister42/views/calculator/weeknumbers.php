<?php

use yii\bootstrap4\Html;
use yii\jui\DatePicker;
use yii\jui\DatePickerLanguageAsset;

$this->title = Yii::t('mr42', 'Week Numbers');
$this->params['breadcrumbs'][] = Yii::t('mr42', 'Calculator');
$this->params['breadcrumbs'][] = $this->title;

$date = new DateTime();
$startWeek = $date->modify('monday this week')->format('c');
$endWeek = $date->modify('sunday this week')->format('c');

echo Html::tag('h1', $this->title);

echo Html::beginTag('div', ['class' => 'row']);
    echo Html::beginTag('div', ['class' => 'col-md']);

        echo Html::beginTag('div', ['class' => 'card mb-3']);
            echo Html::tag('div', Yii::t('mr42', 'Current Week'), ['class' => 'card-header']);
            echo Html::beginTag('div', ['class' => 'card-body']);
                echo Html::tag('div', (int) ($date->format('W')), ['class' => 'card-title h1 display-1 font-weight-bold text-center']);
                echo Html::tag(
                    'div',
                    Yii::t('mr42', 'This week starts at {start} and ends at {end}.', ['start' => Html::tag('b', Yii::$app->formatter->asDate($startWeek, 'full')), 'end' => Html::tag('b', Yii::$app->formatter->asDate($endWeek, 'full'))]),
                    ['class' => 'card-text']
                );
            echo Html::endTag('div');
        echo Html::endTag('div');

        echo Html::beginTag('div', ['class' => 'card mb-3']);
            echo Html::tag('div', Yii::t('mr42', 'Upcoming Weeks'), ['class' => 'card-header']);
            echo Html::beginTag('div', ['class' => 'card-body']);
                for ($x = 1; $x <= 6; $x++) {
                    $date->modify('+1 week');
                    $startWeek = $date->modify('monday this week')->format('c');
                    $endWeek = $date->modify('sunday this week')->format('c');
                    echo Html::tag(
                        'div',
                        Html::tag('b', Yii::t('mr42', 'Week {number}', ['number' => (int) ($date->format('W'))]) . ': ') . Yii::t('mr42', '{start} to {end}', ['start' => Yii::$app->formatter->asDate($startWeek, 'long'), 'end' => Yii::$app->formatter->asDate($endWeek, 'long')]),
                        ['class' => 'clearfix']
                    );
                }
            echo Html::endTag('div');
        echo Html::endTag('div');

    echo Html::endTag('div');

    echo Html::beginTag('div', ['class' => 'col-md']);
        echo DatePicker::widget([
            'clientOptions' => [
                'firstDay' => 1,
                'numberOfMonths' => 2,
                'showWeek' => true,
            ],
            'inline' => true,
            'language' => Yii::$app->language,
        ]);
    echo Html::endTag('div');
echo Html::endTag('div');
