<?php
use yii\bootstrap4\{ActiveForm, Html};

$tab = 1;
$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo Html::beginTag('div', ['class' => 'row form-group']);
	foreach (['firstName', 'lastName'] as $name) :
		echo $form->field($model, $name, [
			'options' => ['class' => 'col-md-6'],
			'inputTemplate' => Yii::$app->icon->inputTemplate('user'),
		])->textInput(['tabindex' => ++$tab]);
	endforeach;
echo Html::endTag('div');

echo $form->field($model, 'fullName', [
		'inputTemplate' => Yii::$app->icon->inputTemplate('user'),
	])->textInput(['tabindex' => ++$tab]);

echo $form->field($model, 'homeAddress', [
		'inputTemplate' => Yii::$app->icon->inputTemplate('home'),
	])->textInput(['tabindex' => ++$tab]);

echo $form->field($model, 'homePhone', [
		'inputTemplate' => Yii::$app->icon->inputTemplate('home'),
	])->input('tel', ['tabindex' => ++$tab]);

echo $form->field($model, 'organization', [
		'inputTemplate' => Yii::$app->icon->inputTemplate('briefcase'),
	])->textInput(['tabindex' => ++$tab]);

echo Html::beginTag('div', ['class' => 'row form-group']);
	foreach (['role' => 'certificate', 'title' => 'bookmark'] as $name => $icon) :
		echo $form->field($model, $name, [
			'options' => ['class' => 'col-md-6'],
			'inputTemplate' => Yii::$app->icon->inputTemplate($icon),
		])->textInput(['tabindex' => ++$tab]);
	endforeach;
echo Html::endTag('div');

echo $form->field($model, 'workAddress', [
		'inputTemplate' => Yii::$app->icon->inputTemplate('home'),
	])->textInput(['tabindex' => ++$tab]);

echo $form->field($model, 'workPhone', [
		'inputTemplate' => Yii::$app->icon->inputTemplate('phone'),
	])->input('tel', ['tabindex' => ++$tab]);

echo $form->field($model, 'email', [
		'inputTemplate' => Yii::$app->icon->inputTemplate('envelope'),
	])->input('email', ['tabindex' => ++$tab]);

echo $form->field($model, 'website', [
		'inputTemplate' => Yii::$app->icon->inputTemplate('globe'),
	])->input('url', ['tabindex' => ++$tab]);

echo $model::getBirthdayCalendar($form, $model, ++$tab);

echo $form->field($model, 'note', [
		'inputTemplate' => Yii::$app->icon->inputTemplate('comment'),
	])->textArea(['rows' => 6, 'tabindex' => ++$tab]);

echo $model->getFormFooter($form, $tab);

ActiveForm::end();
