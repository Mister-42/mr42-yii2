<?php
use app\models\ActiveForm;
use app\widgets\TimePicker;
use yii\bootstrap4\{Alert, Html};

$this->title = Yii::t('mr42', 'Microsoft® Office 365® End Date Calculator');
$this->params['breadcrumbs'][] = Yii::t('mr42', 'Calculator');
$this->params['breadcrumbs'][] = Yii::t('mr42', 'Microsoft® Office 365® End Date');

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-12 col-lg-8 mx-auto']);
		echo Html::tag('h1', $this->title);
		echo Html::tag('div', Yii::t('mr42', 'This calculator calculates the new end date of a Microsoft® Office 365® Open SKU subscription. For redeeming your product keys, please visit {url}.', ['url' => Html::a('https://office.com/setup365', 'https://office.com/setup365', ['class' => 'alert-link'])]), ['class' => 'alert alert-info']);

		if ($flash = Yii::$app->session->getFlash('office365-error')) :
			Alert::begin(['options' => ['class' => 'alert-danger fade show']]);
				echo Html::tag('h4', Yii::t('mr42', 'This action is not allowed!'), ['class' => 'alert-heading']);
				echo Html::tag('div', Yii::t('mr42', 'You cannot renew your subscription for more than three years.'));
				echo Html::tag('div', Yii::t('mr42', 'Theoretically the subscription with {licences} would approximately expire on {date}.', ['licences' => Html::tag('strong', Yii::t('mr42', '{delta, plural, =1{# license} other{# licenses}}', ['delta' => $flash['count']])), 'date' => Html::tag('strong', Yii::$app->formatter->asDate($flash['date'], 'long'))]));
			Alert::end();
		elseif ($flash = Yii::$app->session->getFlash('office365-success')) :
			Alert::begin(['options' => ['class' => 'alert-success']]);
				echo Yii::t('mr42', 'The subscription with {licenses} will approximately expire on {date}.', ['licenses' => Html::tag('strong', Yii::t('mr42', '{delta, plural, =1{# license} other{# licenses}}', ['delta' => $flash['count']])), 'date' => Html::tag('strong', Yii::$app->formatter->asDate($flash['date'], 'long'))]);
			Alert::end();
		endif;

		$form = ActiveForm::begin();
		$tab = 0;

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
					'icon' => 'user',
					'options' => ['class' => 'form-group col-md-6'],
				])
				->input('number', ['class' => 'form-control', 'tabindex' => ++$tab]);
			echo Html::endTag('div');
		endforeach;

		echo $form->field($model, 'action', [
			'icon' => 'cloud',
			'options' => ['class' => 'form-group'],
		])->dropDownList([
			'renew' => Yii::t('mr42', 'Renewing Licenses'),
			'add' => Yii::t('mr42', 'Adding Licenses'),
		], ['tabindex' => ++$tab]);

		echo $form->submitToolbar(Yii::t('mr42', 'Calculate'), $tab);

		ActiveForm::end();
	echo Html::endTag('div');
echo Html::endTag('div');
