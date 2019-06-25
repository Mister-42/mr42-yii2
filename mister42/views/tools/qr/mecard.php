<?php

use app\models\ActiveForm;
use yii\bootstrap4\Html;

$tab = 1;
$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo Html::beginTag('div', ['class' => 'row form-group']);
    foreach (['firstName', 'lastName'] as $name) {
        echo $form->field($model, $name, [
            'icon' => 'user',
            'options' => ['class' => 'col-md-6'],
        ])->textInput(['tabindex' => ++$tab]);
    }
echo Html::endTag('div');

echo Html::beginTag('div', ['class' => 'row form-group']);
    foreach (['firstSound', 'lastSound'] as $name) {
        echo $form->field($model, $name, [
            'icon' => 'assistive-listening-systems',
            'options' => ['class' => 'col-md-6'],
        ])->textInput(['tabindex' => ++$tab]);
    }
echo Html::endTag('div');

echo Html::beginTag('div', ['class' => 'row form-group']);
    foreach (['phone' => 'phone', 'videoPhone' => 'video'] as $name => $icon) {
        echo $form->field($model, $name, [
            'icon' => $icon,
            'options' => ['class' => 'col-md-6'],
        ])->textInput(['tabindex' => ++$tab]);
    }
echo Html::endTag('div');

echo $form->field($model, 'email', [
    'icon' => 'envelope',
])->input('email', ['tabindex' => ++$tab]);

echo $form->field($model, 'note', [
    'icon' => 'comment',
])->textArea(['rows' => 6, 'tabindex' => ++$tab]);

echo $model->getBirthdayCalendar($form, ++$tab);

echo $form->field($model, 'address', [
    'icon' => 'home',
])->textInput(['tabindex' => ++$tab]);

echo $form->field($model, 'website', [
        'icon' => 'globe',
])->input('url', ['tabindex' => ++$tab]);

echo $form->field($model, 'nickname', [
        'icon' => 'user',
])->input('url', ['tabindex' => ++$tab]);

echo $model->getFormFooter($form, $tab);

ActiveForm::end();
