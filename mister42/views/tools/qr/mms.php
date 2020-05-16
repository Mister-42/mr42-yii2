<?php

use mister42\models\ActiveForm;

$tab = 1;
$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo $form->field($model, 'phone', [
    'icon' => 'phone',
])->input('tel', ['tabindex' => ++$tab]);

echo $form->field($model, 'message', [
    'icon' => 'sms',
])->textArea(['rows' => 6, 'tabindex' => ++$tab]);

echo $model->getFormFooter($form, $tab);

ActiveForm::end();
