<?php

use app\models\ActiveForm;
use yii\web\View;

$this->registerJs('$("#qr-authentication").on("change",function(){if($(this).val()=="none"){$(".field-qr-password").addClass("d-none")}else{$(".field-qr-password").removeClass("d-none")}}).change();', View::POS_READY);

$tab = 1;
$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo $form->field($model, 'authentication', [
    'icon' => 'cog',
])->dropDownList($model->getWifiAuthentication(), ['tabindex' => ++$tab]);

echo $form->field($model, 'ssid', [
    'icon' => 'wifi',
])->textInput(['tabindex' => ++$tab]);

echo $form->togglePassword($model, ++$tab, ['class' => 'required']);

echo $form->field($model, 'hidden')->checkBox(['tabindex' => ++$tab]);

echo $model->getFormFooter($form, $tab);

ActiveForm::end();
