<?php
use app\models\Icon;
use yii\bootstrap4\{ActiveForm, Html};

$tab = 1;
$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo $form->field($model, 'email', [
		'template' => '{label}<div class="input-group">'.Icon::fieldAddon('at').'{input}</div>{error}',
	])->input('email', ['tabindex' => ++$tab]);

echo $form->field($model, 'subject', [
		'template' => '{label}<div class="input-group">'.Icon::fieldAddon('heading').'{input}</div>{error}',
	])->textInput(['tabindex' => ++$tab]);

echo $form->field($model, 'message', [
		'template' => '{label}<div class="input-group">'.Icon::fieldAddon('comment').'{input}</div>{error}',
	])->textArea(['rows' => 6, 'tabindex' => ++$tab]);

echo $model->getFormFooter($form, $tab);

ActiveForm::end();
