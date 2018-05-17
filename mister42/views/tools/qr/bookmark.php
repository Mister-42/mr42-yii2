<?php
use yii\bootstrap4\{ActiveForm, Html};

$tab = 1;
$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo $form->field($model, 'title', [
		'template' => '{label}<div class="input-group">'.Yii::$app->icon->fieldAddon('heading').'</span>{input}</div>{error}',
	])->textInput(['tabindex' => ++$tab]);

echo $form->field($model, 'url', [
		'template' => '{label}<div class="input-group">'.Yii::$app->icon->fieldAddon('globe').'{input}</div>{error}',
	])->input('url', ['tabindex' => ++$tab]);

echo $model->getFormFooter($form, $tab);

ActiveForm::end();
