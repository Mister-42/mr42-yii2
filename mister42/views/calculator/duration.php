<?php
use janisto\timepicker\TimePicker;
use yii\bootstrap\{ActiveForm, Alert, Html};

$this->title = 'Date to Date Calculator (duration)';
$this->params['breadcrumbs'][] = 'Calculator';
$this->params['breadcrumbs'][] = 'Date to Date (duration)';

echo '<div class="row">';
	echo '<div class="col-md-offset-2 col-md-8">';
		echo Html::tag('h1', Html::encode($this->title));
		echo Html::tag('p', 'This calculator calculates the number of days between two dates.');

		if ($flash = Yii::$app->session->getFlash('duration-success')) {
			Alert::begin(['options' => ['class' => 'alert-success']]);
			echo '<p>From: ' . Html::tag('b', Yii::$app->formatter->asDate($model->fromDate, 'long')) . '<br>';
			echo 'To: ' . Html::tag('b', Yii::$app->formatter->asDate($model->toDate, 'long')) . '</p>';
			echo '<p>Result: ' . Html::tag('strong', Yii::t('yii', '{delta, plural, =1{1 day} other{# days}}', ['delta' => $flash->days])) . '</p>';
			Alert::end();
		}

		$form = ActiveForm::begin();
		$tab = 1;
		echo '<div class="row">';
		foreach (['fromDate', 'toDate'] as $field) {
			echo $form->field($model, $field, [
				'options' => ['class' => 'col-sm-6'],
			])->widget(TimePicker::classname(), [
				'clientOptions' => [
					'changeMonth' => true,
					'changeYear' => true,
					'dateFormat' => 'yy-mm-dd',
					'firstDay' => 1,
					'yearRange' => '-100Y:+100Y',
				],
				'mode' => 'date',
				'options' => ['class' => 'form-control', 'readonly' => true, 'tabindex' => $tab++],
			]);
		}
		echo '</div>';

		echo Html::tag('div',
			Html::resetButton('Reset', ['class' => 'btn btn-default', 'tabindex' => 4]) . ' ' .
			Html::submitButton('Calculate', ['class' => 'btn btn-primary', 'tabindex' => 3])
		, ['class' => 'form-group text-right']);

		ActiveForm::end();
	echo '</div>';
echo '</div>';
