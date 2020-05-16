<?php

use mister42\models\ActiveForm;

$tab = 1;
$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo $form->field($model, 'id', [
    'icon' => ['name' => 'youtube', 'style' => 'brands'],
])->textInput(['tabindex' => ++$tab]);

echo $model->getFormFooter($form, $tab);

ActiveForm::end();
