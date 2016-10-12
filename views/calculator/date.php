<?php
use yii\bootstrap\{ActiveForm, Alert, Html};
use yii\jui\DatePicker;

$this->title = 'Date';
$this->params['breadcrumbs'][] = 'Calculator';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-md-offset-2 col-md-8">
		<?= Html::tag('h1', Html::encode($this->title)) ?>

		<p>This calculator enables you to add days to a date to calculate a future date.</p>

		<?php
		if ($flash = Yii::$app->session->getFlash('date-success')) {
			$txt = '<p>From: <strong>'. Yii::$app->formatter->asDate($model->from, 'long') . '</strong><br>';
			$txt .= 'Adding: <strong>'. Yii::t('yii', '{delta, plural, =1{1 day} other{# days}}', ['delta' => $model->days]) . '</strong></p>';
			$txt .= '<p>Result: <strong>' . Yii::$app->formatter->asDate($flash, 'long') . '</strong></p>';
			echo Alert::widget(['options' => ['class' => 'alert-success'], 'body' => $txt]);
		}

		$form = ActiveForm::begin();

		echo '<div class="row">';
		echo $form->field($model, 'from', [
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
			'options' => ['class' => 'form-control', 'tabindex' => 1],
		]);

		echo $form->field($model, 'days', [
			'options' => ['class' => 'col-xs-6'],
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('plus').'</span>{input}</div>{error}',
		])->textInput(['tabindex' => 2]);
		echo '</div>'; ?>

		<div class="form-group text-right">
			<?= Html::resetButton('Reset', ['class' => 'btn btn-default', 'tabindex' => 4]) ?>
			<?= Html::submitButton('Calculate', ['class' => 'btn btn-primary', 'tabindex' => 3]) ?>
		</div>

		<?php ActiveForm::end(); ?>

	</div>
</div>
