<?php
use app\models\Form;
use app\widgets\TimePicker;
use yii\bootstrap4\{ActiveForm, Alert, Html};

$this->title = Yii::t('mr42', 'Date Calculator (add/subtract)');
$this->params['breadcrumbs'][] = Yii::t('mr42', 'Calculator');
$this->params['breadcrumbs'][] = Yii::t('mr42', 'Date (add/subtract)');

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-12 col-lg-8 mx-auto']);
		echo Html::tag('h1', $this->title);
		echo Html::tag('div', Yii::t('mr42', 'This calculator enables you to add or subtract days to a date to calculate a future or past date.'), ['class' => 'alert alert-info']);

		if ($flash = Yii::$app->session->getFlash('date-success')) :
			Alert::begin(['options' => ['class' => 'alert-success fade show']]);
				echo Html::tag('div', Yii::t('mr42', 'From: {from}', ['from' => Html::tag('b', Yii::$app->formatter->asDate($model->from, 'long'))]));
				echo Html::tag('div', Yii::t('mr42', 'Adding: {add}', ['add' => Html::tag('b', Yii::t('yii', '{delta, plural, =1{1 day} other{# days}}', ['delta' => $model->days]))]));
				echo Html::tag('div', Yii::t('mr42', 'Result: {result}', ['result' => Html::tag('strong', Yii::$app->formatter->asDate($flash, 'long'))]), ['class' => 'mt-3']);
			Alert::end();
		endif;

		$form = ActiveForm::begin();
		$tab = 0;

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
				'template' => '{label}<div class="input-group">'.Yii::$app->icon->fieldAddon('calendar-plus').'{input}</div>{hint}{error}',
			])	->hint(Yii::t('mr42', 'Use the minus sign (-) to subtract days.'))
				->input('number', ['tabindex' => ++$tab]);
		echo Html::endTag('div');

		echo Form::submitToolbar(Yii::t('mr42', 'Calculate'), $tab);

		ActiveForm::end();
	echo Html::endTag('div');
echo Html::endTag('div');
