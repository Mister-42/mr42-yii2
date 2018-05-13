<?php
use app\models\Icon;
use app\widgets\TimePicker;
use yii\bootstrap4\{ActiveForm, Alert, Html};

$this->title = 'Microsoft® Office 365® End Date Calculator';
$this->params['breadcrumbs'][] = 'Calculator';
$this->params['breadcrumbs'][] = 'Microsoft® Office 365® End Date';

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-12 col-lg-8 mx-auto']);
		echo Html::tag('h1', $this->title);
		echo Html::tag('div', 'This calculator calculates the new end date of a Microsoft® Office 365® Open SKU. For redeeming your product keys, please visit '.Html::a('https://office.com/setup365', 'https://office.com/setup365', ['class' => 'alert-link']).'.', ['class' => 'alert alert-info']);

		if ($flash = Yii::$app->session->getFlash('office365-error')) {
			Alert::begin(['options' => ['class' => 'alert-danger fade show']]);
				echo Html::tag('h4', 'This action is not allowed!', ['class' => 'alert-heading']);
				echo Html::tag('div', 'You cannot renew your subscription for more than three years.');
				echo Html::tag('div', 'Theoretically the subscription with '.Html::tag('strong', Yii::t('site', '{delta, plural, =1{1 license} other{# licenses}}', ['delta' => $flash['count']])).' would approximately expire on '.Html::tag('strong', Yii::$app->formatter->asDate($flash['date'], 'long')));
			Alert::end();
		} elseif ($flash = Yii::$app->session->getFlash('office365-success')) {
			Alert::begin(['options' => ['class' => 'alert-success']]);
				echo 'The subscription with '.Html::tag('strong', Yii::t('site', '{delta, plural, =1{1 license} other{# licenses}}', ['delta' => $flash['count']])).' will approximately expire on '.Html::tag('strong', Yii::$app->formatter->asDate($flash['date'], 'long'));
			Alert::end();
		}

		$form = ActiveForm::begin();

		foreach (['source', 'target'] as $field) :
			echo Html::beginTag('div', ['class' => 'row']);
				echo $form->field($model, $field.'date', [
					'options' => ['class' => 'form-group col-md-6'],
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
					'options' => ['class' => 'form-group col-md-6'],
					'template' => '{label}<div class="input-group">'.Icon::fieldAddon('user').'{input}</div>{error}',
				])
				->input('number', ['class' => 'form-control', 'tabindex' => ++$tab]);
			echo Html::endTag('div');
		endforeach;

		echo $form->field($model, 'action', [
			'options' => ['class' => 'form-group'],
			'template' => '{label}<div class="input-group">'.Icon::fieldAddon('cloud').'{input}</div>{error}',
		])->dropDownList([
			'renew' => 'I am renewing these licenses',
			'add' => 'I am adding these licenses',
		], ['tabindex' => ++$tab]);

		echo Html::tag('div',
			Html::resetButton('Reset', ['class' => 'btn btn-default ml-1', 'tabindex' => $tab + 2]).
			Html::submitButton('Calculate', ['class' => 'btn btn-primary ml-1', 'tabindex' => ++$tab])
		, ['class' => 'btn-toolbar float-right form-group']);

		ActiveForm::end();
	echo Html::endTag('div');
echo Html::endTag('div');
