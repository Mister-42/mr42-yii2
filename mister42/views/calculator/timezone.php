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

$model->source = $model->load(Yii::$app->request->post()) ? $post['source'] : 'Europe/Berlin';
$model->target = $model->load(Yii::$app->request->post()) ? $post['target'] : 'America/New_York';

echo '<div class="row">';
	echo '<div class="col-md-offset-2 col-md-8">';
		echo Html::tag('h1', Html::encode($this->title));
		echo Html::tag('p', 'With this calculator you can check the date and time in any timezone around the globe.');

		if ($flash = Yii::$app->session->getFlash('timezone-success')) {
			Alert::begin(['options' => ['class' => 'alert-success']]);
			echo '<p>' . date('l F j, Y, H:i', strtotime($model->datetime)) . ' in ' . str_replace('_', ' ', $model->source) . '<br>';
			echo 'equals<br>';
			echo Html::tag('strong', $flash->format('l F j, Y, H:i')) . ' in ' . Html::tag('b', str_replace('_', ' ', $model->target)) . '.</p>';
			Alert::end();
		}

		$form = ActiveForm::begin();
		$tab = 1;
		echo '<div class="row">';
		foreach (['source', 'target'] as $field) :
			echo $form->field($model, $field, [
				'options' => ['class' => 'col-sm-6'],
				'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('globe').'</span>{input}</div>{error}',
			])->dropDownList($model->getTimezones(true), ['tabindex' => $tab++]);
		endforeach;
		echo '</div>';

		echo '<div class="row">';
		echo $form->field($model, 'datetime', [
			'options' => ['class' => 'col-sm-6'],
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('time').'</span>{input}</div>{error}',
		])->widget(DateTimePicker::className(), [
			'clientOptions' => [
				'changeMonth' => true,
				'changeYear' => true,
				'firstDay' => 1,
				'timeFormat' => 'HH:mm',
			],
			'dateFormat' => 'yyyy-MM-dd',
			'options' => ['class' => 'form-control', 'readonly' => true, 'tabindex' => $tab++],
		]);
		echo '</div>';

		echo Html::tag('div',
			Html::resetButton('Reset', ['class' => 'btn btn-default', 'tabindex' => 5]) . ' ' .
			Html::submitButton('Convert', ['class' => 'btn btn-primary', 'tabindex' => 4])
		, ['class' => 'form-group text-right']);

		ActiveForm::end();
	echo '</div>';
echo '</div>';
