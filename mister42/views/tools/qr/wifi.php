<?php
use app\models\Icon;
use app\models\tools\Qr;
use yii\bootstrap4\{ActiveForm, Html};
use yii\web\View;

$this->registerJs('$("#qr-authentication").on("change",function(){if($(this).val()=="none"){$(".field-qr-password").addClass("d-none")}else{$(".field-qr-password").removeClass("d-none")}}).change();', View::POS_READY);

$tab = 1;
$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo $form->field($model, 'authentication', [
		'template' => '{label}<div class="input-group">'.Icon::fieldAddon('cog').'{input}</div>{error}',
	])->dropDownList(Qr::getWifiAuthentication(), [
		'tabindex' => ++$tab,
	]);

echo $form->field($model, 'ssid', [
		'template' => '{label}<div class="input-group">'.Icon::fieldAddon('wifi').'{input}</div>{error}',
	])->textInput(['tabindex' => ++$tab]);

echo $form->field($model, 'password', [
		'options' => ['class' => 'required'],
		'template' => '{label}<div class="input-group">'.Icon::fieldAddon('lock').'{input}</div>{error}',
	])->textInput(['tabindex' => ++$tab]);

echo $form->field($model, 'hidden')->checkBox(['tabindex' => ++$tab]);

echo $model->getFormFooter($form, $tab);

ActiveForm::end();
