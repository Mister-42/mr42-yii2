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
    echo Html::beginTag('div', ['class' => 'col']);
        echo Html::beginTag('div', ['class' => 'card mb-3']);
            echo Html::tag('div', Yii::t('mr42', 'Current Week'), ['class' => 'card-header']);
            echo Html::beginTag('div', ['class' => 'card-body']);
                echo Html::tag('h2', (int) ($date->format('W')), ['class' => 'card-title display-1 font-weight-bold text-center']);
                echo Html::beginTag('div', ['class' => 'card-text text-center']);
                    echo Yii::t('mr42', 'This week starts at {start} and ends at {end}.', [
                        'start' => Html::tag('span', Yii::$app->formatter->asDate($startWeek, 'full'), ['class' => 'font-weight-bold']),
                        'end' => Html::tag('span', Yii::$app->formatter->asDate($endWeek, 'full'), ['class' => 'font-weight-bold'])
                    ]);
                echo Html::endTag('div');
            echo Html::endTag('div');
        echo Html::endTag('div');
    echo Html::endTag('div');
echo Html::endTag('div');

echo Html::beginTag('div', ['class' => 'row']);
    echo Html::beginTag('div', ['class' => 'col-xl']);
        echo Html::beginTag('div', ['class' => 'card mb-3']);
            echo Html::tag('div', Yii::t('mr42', 'Upcoming Weeks'), ['class' => 'card-header']);
            echo Html::beginTag('ul', ['class' => 'list-group list-group-flush']);
                for ($x = 1; $x <= 6; $x++) {
                    echo Html::beginTag('li', ['class' => 'list-group-item']);
                        $date->modify('+1 week');
                        $startWeek = $date->modify('monday this week')->format('c');
                        $endWeek = $date->modify('sunday this week')->format('c');
                        echo Html::beginTag('span', ['class' => 'float-left text-left']);
                            echo Yii::t('mr42', '{start} to {end}', [
                                'start' => Html::tag('span', Yii::$app->formatter->asDate($startWeek, 'long'), ['class' => 'text-nowrap']),
                                'end' => Html::tag('span', Yii::$app->formatter->asDate($endWeek, 'long'), ['class' => 'text-nowrap']),
                            ]);
                        echo Html::endTag('span');
                        echo Html::tag('span', Yii::t('mr42', 'Week {number}', ['number' => (int) ($date->format('W'))]), ['class' => 'float-right font-weight-bold']);
                    echo Html::endTag('li');
                }
            echo Html::endTag('ul');
        echo Html::endTag('div');

    echo Html::endTag('div');

    echo Html::beginTag('div', ['class' => 'col-xl']);
        echo Html::beginTag('div', ['class' => 'card mb-3']);
            echo Html::tag('div', Yii::t('mr42', 'Calendar'), ['class' => 'card-header']);
            echo Html::beginTag('div', ['class' => 'card-body mx-auto p-0']);
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
    echo Html::endTag('div');
echo Html::endTag('div');

$this->registerJs("$('.ui-datepicker').addClass('border-0 p-0');");