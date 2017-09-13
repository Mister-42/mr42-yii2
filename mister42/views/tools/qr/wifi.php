<?php
use app\models\tools\Qr;
use yii\bootstrap\{ActiveForm, Html};
use yii\web\View;

$this->registerJs('$("#qr-authentication").on("change",function(){if($(this).val()=="none"){$(".field-qr-password").addClass("hidden")}else{$(".field-qr-password").removeClass("hidden")}}).change();', View::POS_READY);

$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo $form->field($model, 'authentication', [
		'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('cog').'</span>{input}</div>{error}',
	])->dropDownList(Qr::getAuthentication(), [
		'tabindex' => 2,
	]);

echo $form->field($model, 'ssid', [
		'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('signal').'</span>{input}</div>{error}',
	])->textInput(['tabindex' => 3]);

echo $form->field($model, 'password', [
		'options' => ['class' => 'required'],
		'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('lock').'</span>{input}</div>{error}',
	])->textInput(['tabindex' => 4]);

echo $form->field($model, 'hidden')->checkBox(['tabindex' => 5]);

echo $model->getFormFooter($form);

ActiveForm::end();
