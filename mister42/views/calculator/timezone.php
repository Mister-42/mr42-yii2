<?php
use app\models\Icon;
use app\widgets\TimePicker;
use yii\bootstrap4\{ActiveForm, Alert, Html};
use yii\web\View;

$this->title = 'Time Zone Converter';
$this->params['breadcrumbs'][] = 'Calculator';
$this->params['breadcrumbs'][] = $this->title;

$model->source = Yii::$app->request->isPost ? $model->source : 'Europe/Berlin';
$model->target = Yii::$app->request->isPost ? $model->target : 'America/New_York';

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-12 col-lg-8 mx-auto']);
		echo Html::tag('h1', $this->title);
		echo Html::tag('div', 'With this calculator you can check the date and time in any timezone around the globe.', ['class' => 'alert alert-info']);

		if ($flash = Yii::$app->session->getFlash('timezone-success')) {
			Alert::begin(['options' => ['class' => 'alert-success fade show']]);
				echo Html::tag('div', date('l F j, Y, H:i', strtotime($model->datetime)).' in '.str_replace('_', ' ', $model->source));
				echo Html::tag('div', 'equals');
				echo Html::tag('div', Html::tag('strong', $flash->format('l F j, Y, H:i')).' in '.Html::tag('strong', str_replace('_', ' ', $model->target)));
			Alert::end();
		}

		$form = ActiveForm::begin();

		echo Html::beginTag('div', ['class' => 'row']);
			foreach (['source', 'target'] as $field) :
				echo $form->field($model, $field, [
					'options' => ['class' => 'form-group col-md-6'],
					'template' => '{label}<div class="input-group">'.Icon::fieldAddon('globe').'{input}</div>{error}',
				])->dropDownList($model->getTimezones(true), ['tabindex' => ++$tab]);
			endforeach;
		echo Html::endTag('div');

		echo Html::beginTag('div', ['class' => 'row']);
			echo $form->field($model, 'datetime', [
				'options' => ['class' => 'form-group col-md-6'],
			])->widget(TimePicker::class, [
				'addon' => 'clock',
				'clientOptions' => [
					'changeMonth' => true,
					'changeYear' => true,
					'dateFormat' => 'yy-mm-dd',
					'firstDay' => 1,
					'timeFormat' => 'HH:mm',
				],
				'options' => ['class' => 'form-control', 'readonly' => true, 'tabindex' => ++$tab],
			]);
		echo Html::endTag('div');

		echo Html::tag('div',
			Html::resetButton('Reset', ['class' => 'btn btn-default ml-1', 'tabindex' => $tab + 2]).
			Html::submitButton('Convert', ['class' => 'btn btn-primary ml-1', 'tabindex' => ++$tab])
		, ['class' => 'btn-toolbar float-right form-group']);

		ActiveForm::end();
	echo Html::endTag('div');
echo Html::endTag('div');
