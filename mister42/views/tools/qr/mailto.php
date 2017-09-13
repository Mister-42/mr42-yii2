<?php
use yii\bootstrap\{ActiveForm, Html};

$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo $form->field($model, 'email', [
		'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('envelope').'</span>{input}</div>{error}',
	])->input('email', ['tabindex' => 2]);

echo $model->getFormFooter($form);

ActiveForm::end();
