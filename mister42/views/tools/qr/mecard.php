<?php
use app\models\Icon;
use yii\bootstrap4\{ActiveForm, Html};

$tab = 1;
$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo Html::beginTag('div', ['class' => 'row form-group']);
	foreach (['firstName', 'lastName'] as $name) :
		echo $form->field($model, $name, [
			'options' => ['class' => 'col-md-6'],
			'template' => '{label}<div class="input-group">'.Icon::fieldAddon('user').'{input}</div>{error}',
		])->textInput(['tabindex' => ++$tab]);
	endforeach;
echo Html::endTag('div');

echo Html::beginTag('div', ['class' => 'row form-group']);
	foreach (['firstSound', 'lastSound'] as $name) :
		echo $form->field($model, $name, [
			'options' => ['class' => 'col-md-6'],
			'template' => '{label}<div class="input-group">'.Icon::fieldAddon('music').'{input}</div>{error}',
		])->textInput(['tabindex' => ++$tab]);
	endforeach;
echo Html::endTag('div');

echo Html::beginTag('div', ['class' => 'row form-group']);
	foreach (['phone' => 'phone', 'videoPhone' => 'video'] as $name => $icon) :
		echo $form->field($model, $name, [
			'options' => ['class' => 'col-md-6'],
			'template' => '{label}<div class="input-group">'.Icon::fieldAddon($icon).'{input}</div>{error}',
		])->textInput(['tabindex' => ++$tab]);
	endforeach;
echo Html::endTag('div');

echo $form->field($model, 'email', [
		'template' => '{label}<div class="input-group">'.Icon::fieldAddon('envelope').'{input}</div>{error}',
	])->input('email', ['tabindex' => ++$tab]);

echo $form->field($model, 'note', [
		'template' => '{label}<div class="input-group">'.Icon::fieldAddon('comment').'{input}</div>{error}',
	])->textArea(['rows' => 6, 'tabindex' => ++$tab]);

echo $model::getBirthdayCalendar($form, $model, ++$tab);

echo $form->field($model, 'address', [
		'template' => '{label}<div class="input-group">'.Icon::fieldAddon('home').'{input}</div>{error}',
	])->textInput(['tabindex' => ++$tab]);

echo $form->field($model, 'website', [
		'template' => '{label}<div class="input-group">'.Icon::fieldAddon('globe').'{input}</div>{error}',
	])->input('url', ['tabindex' => ++$tab]);

echo $form->field($model, 'nickname', [
		'template' => '{label}<div class="input-group">'.Icon::fieldAddon('user').'{input}</div>{error}',
	])->input('url', ['tabindex' => ++$tab]);

echo $model->getFormFooter($form, $tab);

ActiveForm::end();
