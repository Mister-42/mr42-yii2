<?php

use mister42\models\ActiveForm;
use mister42\widgets\TimePicker;
use yii\bootstrap4\Alert;
use yii\bootstrap4\Html;

$this->title = Yii::t('mr42', 'Date to Date Calculator (duration)');
$this->params['breadcrumbs'][] = Yii::t('mr42', 'Calculator');
$this->params['breadcrumbs'][] = Yii::t('mr42', 'Date to Date (duration)');

echo Html::beginTag('div', ['class' => 'row']);
    echo Html::beginTag('div', ['class' => 'col-md-12 col-lg-8 mx-auto']);
        echo Html::tag('h1', $this->title);
        echo Html::tag('div', Yii::t('mr42', 'This calculator calculates the number of days between two dates.'), ['class' => 'alert alert-info shadow']);

        if ($flash = Yii::$app->session->getFlash('duration-success')) {
            Alert::begin(['options' => ['class' => 'alert-success shadow fade show']]);
            echo Html::tag('div', Yii::t('mr42', 'From: {from}', ['from' => Html::tag('b', Yii::$app->formatter->asDate($model->fromDate, 'long'))]));
            echo Html::tag('div', Yii::t('mr42', 'To: {to}', ['to' => Html::tag('b', Yii::$app->formatter->asDate($model->toDate, 'long'))]));
            echo Html::tag('div', Yii::t('mr42', 'Result: {result}', ['result' => Html::tag('strong', Yii::t('yii', '{delta, plural, =1{1 day} other{# days}}', ['delta' => $flash->days]))]), ['class' => 'mt-3']);
            Alert::end();
        }

        $form = ActiveForm::begin();
        $tab = 0;

        echo Html::beginTag('div', ['class' => 'row']);
            foreach (['fromDate', 'toDate'] as $field) {
                echo $form->field($model, $field, [
                    'options' => ['class' => 'form-group col-md-6'],
                ])->widget(TimePicker::class, [
                    'clientOptions' => [
                        'changeMonth' => true,
                        'changeYear' => true,
                        'dateFormat' => 'yy-mm-dd',
                        'firstDay' => 1,
                        'yearRange' => '-100Y:+100Y',
                    ],
                    'mode' => 'date',
                    'options' => ['class' => 'form-control', 'readonly' => true, 'tabindex' => ++$tab],
                ]);
            }
        echo Html::endTag('div');

        echo $form->submitToolbar(Yii::t('mr42', 'Calculate'), $tab);

        ActiveForm::end();
    echo Html::endTag('div');
echo Html::endTag('div');
