<?php
use yii\bootstrap\{ActiveForm, Html};
use yii\jui\DatePicker;

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

echo $form->field($model, 'sound', [
		'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('music').'</span>{input}</div>{error}',
	])->textInput(['tabindex' => 4]);

echo Html::tag('div',
	$form->field($model, 'phone', [
			'options' => ['class' => 'col-sm-6'],
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('phone-alt').'</span>{input}</div>{error}',
		])->input('tel', ['tabindex' => 5]) .
	$form->field($model, 'videoPhone', [
			'options' => ['class' => 'col-sm-6'],
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('phone-alt').'</span>{input}</div>{error}',
		])->input('tel', ['tabindex' => 6])
, ['class' => 'row']);

echo $form->field($model, 'email', [
		'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('envelope').'</span>{input}</div>{error}',
	])->input('email', ['tabindex' => 7]);

echo $form->field($model, 'note', [
		'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('comment').'</span>{input}</div>{error}',
	])->textArea(['rows' => 6, 'tabindex' => 8]);

echo $form->field($model, 'birthday', [
		'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('calendar').'</span>{input}</div>{error}',
	])->widget(DatePicker::classname(), [
		'clientOptions' => [
			'changeMonth' => true,
			'changeYear' => true,
			'firstDay' => 1,
			'maxDate' => '-0Y',
			'minDate' => '-110Y',
			'yearRange' => '-110Y:-0Y',
		],
		'dateFormat' => 'yyyy-MM-dd',
		'language' => 'en-GB',
		'options' => ['class' => 'form-control', 'readonly' => true, 'tabindex' => 9],
	]);

echo $form->field($model, 'address', [
		'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('home').'</span>{input}</div>{error}',
	])->textInput(['tabindex' => 10]);

echo $form->field($model, 'website', [
		'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('globe').'</span>{input}</div>{error}',
	])->input('url', ['tabindex' => 11]);

echo $form->field($model, 'nickname', [
		'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('user').'</span>{input}</div>{error}',
	])->input('url', ['tabindex' => 12]);

echo $model->getFormFooter($form);

ActiveForm::end();
