<?php

use mister42\models\ActiveForm;

$tab = 1;
$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo $form->field($model, 'title', [
    'icon' => 'heading',
])->textInput(['tabindex' => ++$tab]);

echo $form->field($model, 'url', [
    'icon' => 'globe',
])->input('url', ['tabindex' => ++$tab]);

echo $model->getFormFooter($form, $tab);

ActiveForm::end();
