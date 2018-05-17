<?php
use yii\bootstrap4\{ActiveForm, Html};

$tab = 1;
$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo $form->field($model, 'phone', [
		'template' => '{label}<div class="input-group">'.Yii::$app->icon->fieldAddon('phone').'{input}</div>{error}',
	])->input('tel', ['tabindex' => ++$tab]);

echo $model->getFormFooter($form, $tab);

ActiveForm::end();
