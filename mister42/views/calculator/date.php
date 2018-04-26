<?php
use app\models\{Icon, TimePicker};
use yii\bootstrap4\{ActiveForm, Alert, Html};

$this->title = 'Date Calculator (add/subtract)';
$this->params['breadcrumbs'][] = 'Calculator';
$this->params['breadcrumbs'][] = 'Date (add/subtract)';

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-12 col-lg-8 mx-auto']);
		echo Html::tag('h1', $this->title);
		echo Html::tag('div', 'This calculator enables you to add or subtract days to a date to calculate a future or past date.', ['class' => 'alert alert-info']);

		if ($flash = Yii::$app->session->getFlash('date-success')) {
			Alert::begin(['options' => ['class' => 'alert-success']]);
			echo '<p>From: ' . Html::tag('b', Yii::$app->formatter->asDate($model->from, 'long')) . '<br>';
			echo 'Adding: ' . Html::tag('b', Yii::t('yii', '{delta, plural, =1{1 day} other{# days}}', ['delta' => $model->days])) . '</p>';
			echo Html::tag('p', 'Result: ' . Html::tag('strong', Yii::$app->formatter->asDate($flash, 'long')));
			Alert::end();
		}

		$form = ActiveForm::begin();

		echo Html::beginTag('div', ['class' => 'row']);
			echo $form->field($model, 'from', [
				'options' => ['class' => 'form-group col-md-6'],
			])->widget(TimePicker::class, [
				'clientOptions' => [
					'changeMonth' => true,
					'changeYear' => true,
					'dateFormat' => 'yy-mm-dd',
					'firstDay' => 1,
					'yearRange' => '-100Y:+100Y',
				],
				'mode' => 'date',
				'options' => ['class' => 'form-control', 'readonly' => true, 'tabindex' => ++$tab],
			]);

			echo $form->field($model, 'days', [
				'options' => ['class' => 'form-group col-md-6'],
				'template' => '{label}<div class="input-group">'.Icon::fieldAddon('calendar-plus').'{input}</div>{hint}{error}',
			])	->hint('Use the minus sign (-) to subtract days.')
				->input('number', ['tabindex' => ++$tab]);
		echo Html::endTag('div');

		echo Html::tag('div',
			Html::resetButton('Reset', ['class' => 'btn btn-default ml-1', 'tabindex' => $tab+2]) .
			Html::submitButton('Calculate', ['class' => 'btn btn-primary ml-1', 'tabindex' => ++$tab])
		, ['class' => 'btn-toolbar float-right form-group']);

		ActiveForm::end();
	echo Html::endTag('div');
echo Html::endTag('div');
