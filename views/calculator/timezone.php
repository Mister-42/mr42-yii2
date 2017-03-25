<?php
use phamxuanloc\jui\DateTimePicker;
use yii\bootstrap\{ActiveForm, Alert, Html};
use yii\web\View;

$this->title = 'Time Zone Converter';
$this->params['breadcrumbs'][] = 'Calculator';
$this->params['breadcrumbs'][] = $this->title;

if ($model->load(Yii::$app->request->post())) {
	$post = Yii::$app->request->post('Timezone');
	$this->registerJs('$("#timezone-datetime").val("' . $post['datetime'] . '")', View::POS_READY);
}

$model->source = ($model->load(Yii::$app->request->post())) ? $post['source'] : 'Europe/Berlin';
$model->target = ($model->load(Yii::$app->request->post())) ? $post['target'] : 'America/New_York';
?>
<div class="row">
	<div class="col-md-offset-2 col-md-8"><?php
		echo Html::tag('h1', Html::encode($this->title));
		echo Html::tag('p', 'With this calculator you can check the date and time in any timezone around the globe.');

		if ($flash = Yii::$app->session->getFlash('timezone-success')) {
			Alert::begin(['options' => ['class' => 'alert-success']]);
			echo '<p>' . $model->datetime . ' in ' . str_replace('_', ' ', $model->source) . '<br>';
			echo 'equals<br>';
			echo '<strong>' . $flash->format('Y-m-d H:i') . '</strong> in <b>' . str_replace('_', ' ', $model->target) . '</b>.</p>';
			Alert::end();
		}

		$form = ActiveForm::begin();
		echo '<div class="row">';
		echo $form->field($model, 'source', [
			'options' => ['class' => 'col-sm-6'],
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('time').'</span>{input}</div>{error}',
		])->dropDownList($model->getTimezones(), ['tabindex' => 1]);

		echo $form->field($model, 'datetime', [
			'options' => ['class' => 'col-sm-6'],
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('calendar').'</span>{input}</div>{error}',
		])->widget(DateTimePicker::className(), [
			'clientOptions' => [
				'changeMonth' => true,
				'changeYear' => true,
				'firstDay' => 1,
				'timeFormat' => 'HH:mm',
			],
			'dateFormat' => 'yyyy-MM-dd',
			'options' => ['class' => 'form-control', 'tabindex' => 2],
		]);
		echo '</div>';

		echo '<div class="row">';
		echo $form->field($model, 'target', [
			'options' => ['class' => 'col-sm-6'],
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('time').'</span>{input}</div>{error}',
		])->dropDownList($model->getTimezones(), ['tabindex' => 3]);
		echo '</div>'; ?>

		<div class="form-group text-right">
			<?= Html::resetButton('Reset', ['class' => 'btn btn-default', 'tabindex' => 5]) ?>
			<?= Html::submitButton('Convert', ['class' => 'btn btn-primary', 'tabindex' => 4]) ?>
		</div>

		<?php ActiveForm::end(); ?>
	</div>
</div>
