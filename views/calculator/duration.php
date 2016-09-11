<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Html;
use yii\jui\DatePicker;

$this->title = 'Date to Date';
$this->params['breadcrumbs'][] = 'Calculator';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-md-offset-2 col-md-8">
		<?= Html::tag('h1', Html::encode($this->title)) ?>

		<p>This calculator calculates the number of days between two dates.</p>

		<?php
		if ($flash = Yii::$app->session->getFlash('duration-success')) {
			$txt = '<p>From: <strong>'. Yii::$app->formatter->asDate($model->from, 'long') . '</strong><br />';
			$txt .= 'To: <strong>'. Yii::$app->formatter->asDate($model->to, 'long') . '</strong></p>';
			$txt .= '<p>Result: <strong>' . Yii::t('yii', '{delta, plural, =1{1 day} other{# days}}', ['delta' => $flash->days]) . '</strong></p>';
			echo Alert::widget(['options' => ['class' => 'alert-success'], 'body' => $txt]);
		}

		$form = ActiveForm::begin();

		echo '<div class="row">';
		$tab = 0;
		foreach (['from', 'to'] as $field) {
			$tab++;
			echo $form->field($model, $field, [
				'options' => ['class' => 'col-xs-6'],
				'template' => '{label}<div class="input-group"><span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>{input}</div>{error}',
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
