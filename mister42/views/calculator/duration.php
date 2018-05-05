<?php
use app\models\Icon;
use app\widgets\TimePicker;
use yii\bootstrap4\{ActiveForm, Alert, Html};

$this->title = 'Date to Date Calculator (duration)';
$this->params['breadcrumbs'][] = 'Calculator';
$this->params['breadcrumbs'][] = 'Date to Date (duration)';

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-12 col-lg-8 mx-auto']);
		echo Html::tag('h1', $this->title);
		echo Html::tag('div', 'This calculator calculates the number of days between two dates.', ['class' => 'alert alert-info']);

		if ($flash = Yii::$app->session->getFlash('duration-success')) {
			Alert::begin(['options' => ['class' => 'alert-success']]);
			echo '<p>From: ' . Html::tag('b', Yii::$app->formatter->asDate($model->fromDate, 'long')) . '<br>';
			echo 'To: ' . Html::tag('b', Yii::$app->formatter->asDate($model->toDate, 'long')) . '</p>';
			echo Html::tag('p', 'Result: ' . Html::tag('strong', Yii::t('yii', '{delta, plural, =1{1 day} other{# days}}', ['delta' => $flash->days])));
			Alert::end();
		}

		$form = ActiveForm::begin();

		echo Html::beginTag('div', ['class' => 'row']);
			foreach (['fromDate', 'toDate'] as $field) {
				echo $form->field($model, $field, [
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
			}
		echo Html::endTag('div');

		echo Html::tag('div',
			Html::resetButton('Reset', ['class' => 'btn btn-default ml-1', 'tabindex' => $tab+2]) .
			Html::submitButton('Calculate', ['class' => 'btn btn-primary ml-1', 'tabindex' => ++$tab])
		, ['class' => 'btn-toolbar float-right form-group']);

		ActiveForm::end();
	echo Html::endTag('div');
echo Html::endTag('div');
