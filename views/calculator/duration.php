<?php
use yii\bootstrap\{ActiveForm, Alert, Html};
use yii\jui\DatePicker;

$this->title = 'Date to Date (duration)';
$this->params['breadcrumbs'][] = 'Calculator';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-md-offset-2 col-md-8"><?php
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

		echo '<div class="row">';
		$tab = 0;
		foreach (['fromDate', 'toDate'] as $field) {
			$tab++;
			echo $form->field($model, $field, [
				'options' => ['class' => 'col-xs-6'],
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
				'options' => ['class' => 'form-control', 'tabindex' => $tab],
			]);
		}
		echo '</div>'; ?>

		<div class="form-group text-right">
			<?= Html::resetButton('Reset', ['class' => 'btn btn-default', 'tabindex' => 4]) ?>
			<?= Html::submitButton('Calculate', ['class' => 'btn btn-primary', 'tabindex' => 3]) ?>
		</div>

		<?php ActiveForm::end(); ?>

	</div>
</div>
