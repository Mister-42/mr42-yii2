<?php
use yii\bootstrap4\{ActiveForm, Html};

$tab = 1;
$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo $form->field($model, 'address', [
		'template' => '{label}<div class="input-group">'.Yii::$app->icon->fieldAddon('address-card').'{input}</div>{error}',
	])->textInput(['tabindex' => ++$tab]);

echo $form->field($model, 'amount', [
		'template' => '{label}<div class="input-group">'.Yii::$app->icon->fieldAddon('btc', ['style' => 'brands']).'{input}</div>{error}',
	])->input('number', ['step' => '0.00000001', 'tabindex' => ++$tab]);

echo $form->field($model, 'name', [
		'template' => '{label}<div class="input-group">'.Yii::$app->icon->fieldAddon('user').'{input}</div>{error}',
	])->textInput(['tabindex' => ++$tab]);

echo $form->field($model, 'message', [
		'template' => '{label}<div class="input-group">'.Yii::$app->icon->fieldAddon('comment').'{input}</div>{error}',
	])->textArea(['rows' => 6, 'tabindex' => ++$tab]);

echo $model->getFormFooter($form, $tab);

ActiveForm::end();
