<?php
use app\models\Icon;
use yii\bootstrap4\{ActiveForm, Html};

$tab = 1;
$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo $form->field($model, 'address', [
		'template' => '{label}<div class="input-group">'.Icon::fieldAddon('address-card').'{input}</div>{error}',
	])->textInput(['tabindex' => ++$tab]);

echo $form->field($model, 'amount', [
		'template' => '{label}<div class="input-group">'.Icon::fieldAddon('bold').'{input}</div>{error}',
	])->input('number', ['step' => '0.00000001', 'tabindex' => ++$tab]);

echo $form->field($model, 'name', [
		'template' => '{label}<div class="input-group">'.Icon::fieldAddon('user').'{input}</div>{error}',
	])->textInput(['tabindex' => ++$tab]);

echo $form->field($model, 'message', [
		'template' => '{label}<div class="input-group">'.Icon::fieldAddon('comment').'{input}</div>{error}',
	])->textArea(['rows' => 6, 'tabindex' => ++$tab]);

echo $model->getFormFooter($form, $tab);

ActiveForm::end();
