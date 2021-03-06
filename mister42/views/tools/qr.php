<?php

use mister42\models\ActiveForm;
use mister42\widgets\TimePicker;
use yii\bootstrap4\Alert;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\Pjax;

$this->title = Yii::t('mr42', 'QR Code Generator');
$this->params['breadcrumbs'][] = Yii::t('mr42', 'Tools');
$this->params['breadcrumbs'][] = $this->title;

TimePicker::widget();

echo Html::beginTag('div', ['class' => 'row']);
    echo Html::beginTag('div', ['class' => 'col-lg-8 mx-auto']);
        echo Html::tag('h1', $this->title);
        if ($qr = Yii::$app->session->getFlash('qr-success')) {
            $imgSize = min(250, $model->size);
            Alert::begin(['options' => ['class' => 'alert-success shadow fade show clearfix']]);
            echo Html::img(Url::to('@assets/temp/' . basename($qr)), ['alt' => Yii::t('mr42', '{type} QR Code', ['type' => $model->type]), 'class' => 'float-left mr-2', 'height' => $imgSize, 'width' => $imgSize]);
            echo Html::tag('div', Yii::t('mr42', 'Your QR Code has been generated successfully.'));
            echo Html::tag('div', Yii::t('mr42', 'Do not link to the image on this website directly as it will be deleted shortly.'));
            Alert::end();
        }

        if ($bytes = Yii::$app->session->getFlash('qr-size')) {
            Alert::begin(['options' => ['class' => 'alert-danger shadow']]);
            echo Yii::t('mr42', 'Too much information: Try to decrease the size by {bytes} characters.', ['bytes' => $bytes]);
            Alert::end();
        }

        $form = ActiveForm::begin();
        echo $form->field($model, 'type', [
            'icon' => 'th-list',
        ])->dropDownList($model->getTypes(), [
            'prompt' => Yii::t('mr42', 'Select a Type'),
            'onchange' => new JsExpression("if(!this.value){\$('#form').empty()}else{\$('#form').load('',{'type':this.value})}"),
            'tabindex' => 1,
        ]);
        ActiveForm::end();

        Pjax::begin(['id' => 'form']);
            echo $qrForm;
        Pjax::end();
    echo Html::endTag('div');
echo Html::endTag('div');
