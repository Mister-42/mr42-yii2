<?php
use yii\bootstrap\{ActiveForm, Html};

$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo $form->field($model, 'title', [
		'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('header').'</span>{input}</div>{error}',
	])->textInput(['tabindex' => 2]);

echo $form->field($model, 'url', [
		'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('globe').'</span>{input}</div>{error}',
	])->input('url', ['tabindex' => 3]);

echo $model->getFormFooter($form);

ActiveForm::end();
