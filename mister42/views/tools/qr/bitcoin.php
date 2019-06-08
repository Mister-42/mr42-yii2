<?php
use yii\bootstrap4\{ActiveForm, Html};

$tab = 1;
$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo $form->field($model, 'address', [
		'inputTemplate' => Yii::$app->icon->inputTemplate('address-card'),
	])->textInput(['tabindex' => ++$tab]);

echo $form->field($model, 'amount', [
		'inputTemplate' => Yii::$app->icon->inputTemplate('btc', ['style' => 'brands']),
	])->input('number', ['step' => '0.00000001', 'tabindex' => ++$tab]);

echo $form->field($model, 'name', [
		'inputTemplate' => Yii::$app->icon->inputTemplate('user'),
	])->textInput(['tabindex' => ++$tab]);

echo $form->field($model, 'message', [
		'inputTemplate' => Yii::$app->icon->inputTemplate('comment'),
	])->textArea(['rows' => 6, 'tabindex' => ++$tab]);

echo $model->getFormFooter($form, $tab);

ActiveForm::end();
