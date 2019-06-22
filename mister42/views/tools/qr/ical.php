<?php

use app\models\ActiveForm;
use app\widgets\TimePicker;
use yii\bootstrap4\Html;

$tab = 1;
$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo Html::beginTag('div', ['class' => 'row form-group']);
	foreach (['start', 'end'] as $name) {
		echo $form->field($model, $name, [
			'options' => ['class' => 'col-md-6'],
		])->widget(TimePicker::class, [
			'clientOptions' => [
				'changeMonth' => true,
				'changeYear' => true,
				'dateFormat' => 'yy-mm-dd',
				'firstDay' => 1,
				'timeFormat' => 'HH:mm',
			],
			'options' => ['class' => 'form-control', 'readonly' => true, 'tabindex' => ++$tab],
		]);
	}
echo Html::endTag('div');

echo $form->field($model, 'summary', [
	'icon' => 'comment',
])->textArea(['rows' => 6, 'tabindex' => ++$tab]);

echo $model->getFormFooter($form, $tab);

ActiveForm::end();
