<?php
use janisto\timepicker\TimePicker;
use yii\bootstrap\{ActiveForm, Alert, Html};
use yii\web\View;

$this->title = 'Time Zone Converter';
$this->params['breadcrumbs'][] = 'Calculator';
$this->params['breadcrumbs'][] = $this->title;

$model->source = Yii::$app->request->isPost ? $model->source : 'Europe/Berlin';
$model->target = Yii::$app->request->isPost ? $model->target : 'America/New_York';

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-offset-2 col-md-8']);
		echo Html::tag('h1', Html::encode($this->title));
		echo Html::tag('div', 'With this calculator you can check the date and time in any timezone around the globe.', ['class' => 'alert alert-info']);

		if ($flash = Yii::$app->session->getFlash('timezone-success')) {
			Alert::begin(['options' => ['class' => 'alert-success']]);
				echo Html::tag('p', date('l F j, Y, H:i', strtotime($model->datetime)) . ' in ' . str_replace('_', ' ', $model->source));
				echo Html::tag('p', 'equals');
				echo Html::tag('p', Html::tag('strong', $flash->format('l F j, Y, H:i')) . ' in ' . Html::tag('b', str_replace('_', ' ', $model->target)));
			Alert::end();
		}

		$form = ActiveForm::begin();

		echo Html::beginTag('div', ['class' => 'row']);
			foreach (['source', 'target'] as $field) :
				echo $form->field($model, $field, [
					'options' => ['class' => 'col-sm-6'],
					'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('globe').'</span>{input}</div>{error}',
				])->dropDownList($model->getTimezones(true), ['tabindex' => ++$tab]);
			endforeach;
		echo Html::endTag('div');

		echo Html::beginTag('div', ['class' => 'row']);
			echo $form->field($model, 'datetime', [
				'options' => ['class' => 'col-sm-6'],
			])->widget(TimePicker::class, [
				'addon' => Html::icon('time'),
				'clientOptions' => [
					'changeMonth' => true,
					'changeYear' => true,
					'dateFormat' => 'yy-mm-dd',
					'firstDay' => 1,
					'timeFormat' => 'HH:mm',
				],
				'options' => ['class' => 'form-control', 'readonly' => true, 'tabindex' => ++$tab],
			]);
		echo Html::endTag('div');

		echo Html::tag('div',
			Html::resetButton('Reset', ['class' => 'btn btn-default', 'tabindex' => 5]) .
			Html::submitButton('Convert', ['class' => 'btn btn-primary', 'tabindex' => 4])
		, ['class' => 'btn-toolbar form-group pull-right']);

		ActiveForm::end();
	echo Html::endTag('div');
echo Html::endTag('div');
