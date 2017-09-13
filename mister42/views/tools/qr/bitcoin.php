<?php
use yii\bootstrap\{ActiveForm, Html};

$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo $form->field($model, 'address', [
		'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('home').'</span>{input}</div>{error}',
	])->textInput(['tabindex' => 2]);

echo $form->field($model, 'amount', [
		'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('bitcoin').'</span>{input}</div>{error}',
	])->input('number', ['tabindex' => 3]);

echo $form->field($model, 'name', [
		'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('user').'</span>{input}</div>{error}',
	])->textInput(['tabindex' => 4]);

echo $form->field($model, 'message', [
		'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('comment').'</span>{input}</div>{error}',
	])->textArea(['rows' => 6, 'tabindex' => 5]);

echo $model->getFormFooter($form);

ActiveForm::end();
