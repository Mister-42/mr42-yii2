<?php
use app\models\Form;
use app\models\tools\Qr;
use yii\bootstrap4\{ActiveForm, Html};
use yii\web\View;

$this->registerJs('$("#qr-authentication").on("change",function(){if($(this).val()=="none"){$(".field-qr-password").addClass("d-none")}else{$(".field-qr-password").removeClass("d-none")}}).change();', View::POS_READY);

$tab = 1;
$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo $form->field($model, 'authentication', [
		'inputTemplate' => Yii::$app->icon->inputTemplate('cog'),
	])->dropDownList(Qr::getWifiAuthentication(), [
		'tabindex' => ++$tab,
	]);

echo $form->field($model, 'ssid', [
		'inputTemplate' => Yii::$app->icon->inputTemplate('wifi'),
	])->textInput(['tabindex' => ++$tab]);

echo Form::togglePassword($form, $model, $this, ['class' => 'required', 'tab' => ++$tab]);

echo $form->field($model, 'hidden')->checkBox(['tabindex' => ++$tab]);

echo $model->getFormFooter($form, $tab);

ActiveForm::end();
