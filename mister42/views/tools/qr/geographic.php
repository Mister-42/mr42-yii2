<?php

use app\models\ActiveForm;
use yii\bootstrap4\Html;

$tab = 1;
$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo Html::beginTag('div', ['class' => 'row form-group']);
    foreach (['lat', 'lng', 'altitude'] as $name) {
        echo $form->field($model, $name, [
            'icon' => 'map-marker',
            'options' => ['class' => 'col-md-4'],
        ])->input('number', ['step' => '0.000001', 'tabindex' => ++$tab]);
    }
echo Html::endTag('div');

echo $model->getFormFooter($form, $tab);

ActiveForm::end();
