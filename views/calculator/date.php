<?php
use yii\bootstrap\{ActiveForm, Alert, Html};
use yii\jui\DatePicker;

$this->title = 'Date Calculator (add/subtract)';
$this->params['breadcrumbs'][] = 'Calculator';
$this->params['breadcrumbs'][] = 'Date (add/subtract)';
?>
<div class="row">
	<div class="col-md-offset-2 col-md-8"><?php
		echo Html::tag('h1', Html::encode($this->title));
		echo Html::tag('p', 'This calculator enables you to add or subtract days to a date to calculate a future or past date.');

		if ($flash = Yii::$app->session->getFlash('date-success')) {
			Alert::begin(['options' => ['class' => 'alert-success']]);
			echo '<p>From: ' . Html::tag('b', Yii::$app->formatter->asDate($model->from, 'long')) . '<br>';
			echo 'Adding: ' . Html::tag('b', Yii::t('yii', '{delta, plural, =1{1 day} other{# days}}', ['delta' => $model->days])) . '</p>';
			echo '<p>Result: ' . Html::tag('strong', Yii::$app->formatter->asDate($flash, 'long')) . '</p>';
			Alert::end();
		}

		$form = ActiveForm::begin();

		echo '<div class="row">';
		echo $form->field($model, 'from', [
			'options' => ['class' => 'col-sm-6'],
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('calendar').'</span>{input}</div>{error}',
		])->widget(DatePicker::classname(), [
			'clientOptions' => [
				'changeMonth' => true,
				'changeYear' => true,
				'firstDay' => 1,
				'yearRange' => '-100Y:+100Y',
			],
			'dateFormat' => 'yyyy-MM-dd',
			'language' => 'en-GB',
			'options' => ['class' => 'form-control', 'tabindex' => 1],
		]);

		echo $form->field($model, 'days', [
			'options' => ['class' => 'col-sm-6'],
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('plus').'</span>{input}</div>{error}',
		])->input('number', ['tabindex' => 2]);
		echo '</div>'; ?>

		<div class="form-group text-right">
			<?= Html::resetButton('Reset', ['class' => 'btn btn-default', 'tabindex' => 4]) ?>
			<?= Html::submitButton('Calculate', ['class' => 'btn btn-primary', 'tabindex' => 3]) ?>
		</div>

		<?php ActiveForm::end(); ?>

	</div>
</div>
