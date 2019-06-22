<?php

use app\models\ActiveForm;
use yii\bootstrap4\Html;

$tab = 1;
$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo Html::beginTag('div', ['class' => 'row form-group']);
	foreach (['firstName', 'lastName'] as $name) {
		echo $form->field($model, $name, [
			'icon' => 'user',
			'options' => ['class' => 'col-md-6'],
		])->textInput(['tabindex' => ++$tab]);
	}
echo Html::endTag('div');

echo $form->field($model, 'fullName', [
	'icon' => 'user',
])->textInput(['tabindex' => ++$tab]);

echo $form->field($model, 'homeAddress', [
	'icon' => 'home',
])->textInput(['tabindex' => ++$tab]);

echo $form->field($model, 'homePhone', [
	'icon' => 'home',
])->input('tel', ['tabindex' => ++$tab]);

echo $form->field($model, 'organization', [
	'icon' => 'briefcase',
])->textInput(['tabindex' => ++$tab]);

echo Html::beginTag('div', ['class' => 'row form-group']);
	foreach (['role' => 'certificate', 'title' => 'bookmark'] as $name => $icon) {
		echo $form->field($model, $name, [
			'icon' => $icon,
			'options' => ['class' => 'col-md-6'],
		])->textInput(['tabindex' => ++$tab]);
	}
echo Html::endTag('div');

echo $form->field($model, 'workAddress', [
	'icon' => 'home',
])->textInput(['tabindex' => ++$tab]);

echo $form->field($model, 'workPhone', [
	'icon' => 'phone',
])->input('tel', ['tabindex' => ++$tab]);

echo $form->field($model, 'email', [
	'icon' => 'envelope',
])->input('email', ['tabindex' => ++$tab]);

echo $form->field($model, 'website', [
	'icon' => 'globe',
])->input('url', ['tabindex' => ++$tab]);

echo $model->getBirthdayCalendar($form, ++$tab);

echo $form->field($model, 'note', [
	'icon' => 'comment',
])->textArea(['rows' => 6, 'tabindex' => ++$tab]);

echo $model->getFormFooter($form, $tab);

ActiveForm::end();
