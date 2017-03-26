<?php
use yii\bootstrap\{ActiveForm, Alert, Html};
use yii\jui\DatePicker;

$this->title = 'Microsoft® Office 365® End Date Calculator';
$this->params['breadcrumbs'][] = 'Calculator';
$this->params['breadcrumbs'][] = 'Microsoft® Office 365® End Date';

echo '<div class="row">';
	echo '<div class="col-md-offset-2 col-md-8">';
		echo Html::tag('h1', Html::encode($this->title));
		echo Html::tag('p', 'This calculator calculates the new end date of a Microsoft® Office 365® Open SKU.');

		if ($flash = Yii::$app->session->getFlash('office365-error')) {
			Alert::begin(['options' => ['class' => 'alert-danger']]);
			echo '<p><b>This action is not allowed.</b> Subscriptions have a maximum end date of 3 years into the future.</p>';
			echo '<p>Theoretically the subscription with ' . Html::tag('strong', Yii::t('site', '{delta, plural, =1{1 license} other{# licenses}}', ['delta' => $flash['count']])) . ' would approximately expire on ' . Html::tag('strong', Yii::$app->formatter->asDate($flash['date'], 'long')) . '.</p>';
			Alert::end();
		} elseif ($flash = Yii::$app->session->getFlash('office365-success')) {
			Alert::begin(['options' => ['class' => 'alert-success']]);
			echo '<p>The subscription with ' . Html::tag('strong', Yii::t('site', '{delta, plural, =1{1 license} other{# licenses}}', ['delta' => $flash['count']])) . ' will approximately expire on ' . Html::tag('strong', Yii::$app->formatter->asDate($flash['date'], 'long')) . '.</p>';
			Alert::end();
		}

		$form = ActiveForm::begin();
		$tab = 1;
		foreach (['source', 'target'] as $field) {
			echo '<div class="row">';
			echo $form->field($model, $field.'date', [
				'options' => ['class' => 'col-sm-6'],
				'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('calendar').'</span>{input}</div>{error}',
			])->widget(DatePicker::classname(), [
				'clientOptions' => [
					'changeMonth' => true,
					'changeYear' => true,
					'firstDay' => 1,
					'yearRange' => '-2Y:+3Y',
				],
				'dateFormat' => 'yyyy-MM-dd',
				'language' => 'en-GB',
				'options' => ['class' => 'form-control', 'readonly' => true, 'tabindex' => $tab++],
			]);

			echo $form->field($model, $field.'count', [
				'options' => ['class' => 'col-sm-6'],
				'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('user').'</span>{input}</div>{error}',
			])
			->input('number', ['class' => 'form-control', 'tabindex' => $tab++]);
			echo '</div>';
		}

		echo $form->field($model, 'action')->dropDownList([
			'renew' => 'I am renewing these licenses',
			'add' => 'I am adding these licenses',
		], ['tabindex' => $tab++]);

		echo Html::tag('div',
			Html::resetButton('Reset', ['class' => 'btn btn-default', 'tabindex' => 7]) . ' ' .
			Html::submitButton('Calculate', ['class' => 'btn btn-primary', 'tabindex' => 6])
		, ['class' => 'form-group text-right']);

		ActiveForm::end();
	echo '</div>';
echo '</div>';
