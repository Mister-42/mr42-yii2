<?php
use yii\bootstrap\{ActiveForm, Html};

$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo Html::tag('div',
	$form->field($model, 'firstName', [
			'options' => ['class' => 'col-sm-6'],
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('user').'</span>{input}</div>{error}',
		])->textInput(['tabindex' => 2]) .
	$form->field($model, 'lastName', [
			'options' => ['class' => 'col-sm-6'],
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('user').'</span>{input}</div>{error}',
		])->textInput(['tabindex' => 3])
, ['class' => 'row']);

echo Html::tag('div',
	$form->field($model, 'firstSound', [
			'options' => ['class' => 'col-sm-6'],
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('music').'</span>{input}</div>{error}',
		])->textInput(['tabindex' => 4]) .
	$form->field($model, 'lastSound', [
			'options' => ['class' => 'col-sm-6'],
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('music').'</span>{input}</div>{error}',
		])->textInput(['tabindex' => 5])
, ['class' => 'row']);

echo Html::tag('div',
	$form->field($model, 'phone', [
			'options' => ['class' => 'col-sm-6'],
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('phone-alt').'</span>{input}</div>{error}',
		])->input('tel', ['tabindex' => 6]) .
	$form->field($model, 'videoPhone', [
			'options' => ['class' => 'col-sm-6'],
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('phone-alt').'</span>{input}</div>{error}',
		])->input('tel', ['tabindex' => 7])
, ['class' => 'row']);

echo $form->field($model, 'email', [
		'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('envelope').'</span>{input}</div>{error}',
	])->input('email', ['tabindex' => 8]);

echo $form->field($model, 'note', [
		'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('comment').'</span>{input}</div>{error}',
	])->textArea(['rows' => 6, 'tabindex' => 9]);

echo $model::getBirthdayCalendar($form, $model, 10);

echo $form->field($model, 'address', [
		'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('home').'</span>{input}</div>{error}',
	])->textInput(['tabindex' => 11]);

echo $form->field($model, 'website', [
		'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('globe').'</span>{input}</div>{error}',
	])->input('url', ['tabindex' => 12]);

echo $form->field($model, 'nickname', [
		'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('user').'</span>{input}</div>{error}',
	])->input('url', ['tabindex' => 13]);

echo $model->getFormFooter($form);

ActiveForm::end();
