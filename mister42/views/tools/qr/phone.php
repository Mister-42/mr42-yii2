<?php
use app\models\ActiveForm;
use yii\bootstrap4\Html;

$tab = 1;
$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo $form->field($model, 'phone', [
	'icon' => 'phone',
])->input('tel', ['tabindex' => ++$tab]);

echo $model->getFormFooter($form, $tab);

ActiveForm::end();
