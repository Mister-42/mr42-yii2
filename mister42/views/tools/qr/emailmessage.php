<?php
use yii\bootstrap4\{ActiveForm, Html};

$tab = 1;
$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo $form->field($model, 'email', [
		'inputTemplate' => Yii::$app->icon->inputTemplate('at'),
	])->input('email', ['tabindex' => ++$tab]);

echo $form->field($model, 'subject', [
		'inputTemplate' => Yii::$app->icon->inputTemplate('heading'),
	])->textInput(['tabindex' => ++$tab]);

echo $form->field($model, 'message', [
		'inputTemplate' => Yii::$app->icon->inputTemplate('comment'),
	])->textArea(['rows' => 6, 'tabindex' => ++$tab]);

echo $model->getFormFooter($form, $tab);

ActiveForm::end();
