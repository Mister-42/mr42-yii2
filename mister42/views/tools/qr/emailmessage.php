<?php

use app\models\ActiveForm;

$tab = 1;
$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo $form->field($model, 'email', [
    'icon' => 'at',
])->input('email', ['tabindex' => ++$tab]);

echo $form->field($model, 'subject', [
    'icon' => 'heading',
])->textInput(['tabindex' => ++$tab]);

echo $form->field($model, 'message', [
    'icon' => 'envelope',
])->textArea(['rows' => 6, 'tabindex' => ++$tab]);

echo $model->getFormFooter($form, $tab);

ActiveForm::end();
