<?php

use app\models\ActiveForm;

$tab = 1;
$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo $form->field($model, 'address', [
    'icon' => 'address-card',
])->textInput(['tabindex' => ++$tab]);

echo $form->field($model, 'amount', [
    'icon' => ['name' => 'btc', 'style' => 'brands'],
])->input('number', ['step' => '0.00000001', 'tabindex' => ++$tab]);

echo $form->field($model, 'name', [
    'icon' => 'user',
])->textInput(['tabindex' => ++$tab]);

echo $form->field($model, 'message', [
    'icon' => 'comment',
])->textArea(['rows' => 6, 'tabindex' => ++$tab]);

echo $model->getFormFooter($form, $tab);

ActiveForm::end();
