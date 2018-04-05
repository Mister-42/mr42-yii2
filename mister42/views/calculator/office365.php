<?php
use janisto\timepicker\TimePicker;
use yii\bootstrap\{ActiveForm, Alert, Html};

$this->title = 'Microsoft® Office 365® End Date Calculator';
$this->params['breadcrumbs'][] = 'Calculator';
$this->params['breadcrumbs'][] = 'Microsoft® Office 365® End Date';

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-offset-2 col-md-8']);
		echo Html::tag('h1', Html::encode($this->title));
		echo Html::tag('div', 'This calculator calculates the new end date of a Microsoft® Office 365® Open SKU. For redeeming your product keys, please visit ' . Html::a('https://office.com/setup365', 'https://office.com/setup365') . '.', ['class' => 'alert alert-info']);

		if ($flash = Yii::$app->session->getFlash('office365-error')) {
			Alert::begin(['options' => ['class' => 'alert-danger']]);
				echo Html::tag('p', Html::tag('strong', 'This action is not allowed!') . ' You cannot renew your subscription for more than three years.');
				echo Html::tag('p', 'Theoretically the subscription with ' . Html::tag('strong', Yii::t('site', '{delta, plural, =1{1 license} other{# licenses}}', ['delta' => $flash['count']])) . ' would approximately expire on ' . Html::tag('strong', Yii::$app->formatter->asDate($flash['date'], 'long')));
			Alert::end();
		} elseif ($flash = Yii::$app->session->getFlash('office365-success')) {
			Alert::begin(['options' => ['class' => 'alert-success']]);
				echo 'The subscription with ' . Html::tag('strong', Yii::t('site', '{delta, plural, =1{1 license} other{# licenses}}', ['delta' => $flash['count']])) . ' will approximately expire on ' . Html::tag('strong', Yii::$app->formatter->asDate($flash['date'], 'long'));
			Alert::end();
		}

		$form = ActiveForm::begin();

		foreach (['source', 'target'] as $field) :
			echo Html::beginTag('div', ['class' => 'row']);
				echo $form->field($model, $field.'date', [
					'options' => ['class' => 'col-sm-6'],
				])->widget(TimePicker::class, [
					'clientOptions' => [
						'changeMonth' => true,
						'changeYear' => true,
						'dateFormat' => 'yy-mm-dd',
						'firstDay' => 1,
						'yearRange' => '-2Y:+3Y',
					],
					'mode' => 'date',
					'options' => ['class' => 'form-control', 'readonly' => true, 'tabindex' => ++$tab],
				]);

				echo $form->field($model, $field.'count', [
					'options' => ['class' => 'col-sm-6'],
					'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('user').'</span>{input}</div>{error}',
				])
				->input('number', ['class' => 'form-control', 'tabindex' => ++$tab]);
			echo Html::endTag('div');
		endforeach;

		echo $form->field($model, 'action')->dropDownList([
			'renew' => 'I am renewing these licenses',
			'add' => 'I am adding these licenses',
		], ['tabindex' => ++$tab]);

		echo Html::tag('div',
			Html::resetButton('Reset', ['class' => 'btn btn-default', 'tabindex' => 7]) .
			Html::submitButton('Calculate', ['class' => 'btn btn-primary', 'tabindex' => 6])
		, ['class' => 'btn-toolbar form-group pull-right']);

		ActiveForm::end();
	echo Html::endTag('div');
echo Html::endTag('div');
