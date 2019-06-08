<?php
use yii\bootstrap4\{ActiveForm, Html};

$tab = 1;
$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo $form->field($model, 'title', [
		'inputTemplate' => Yii::$app->icon->inputTemplate('heading'),
	])->textInput(['tabindex' => ++$tab]);

echo $form->field($model, 'url', [
		'inputTemplate' => Yii::$app->icon->inputTemplate('globe'),
	])->input('url', ['tabindex' => ++$tab]);

echo $model->getFormFooter($form, $tab);

ActiveForm::end();
