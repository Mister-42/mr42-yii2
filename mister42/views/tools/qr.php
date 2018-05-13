<?php
use app\models\Icon;
use app\models\tools\Qr;
use app\widgets\TimePicker;
use yii\bootstrap4\{ActiveForm, Alert, Html};
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\Pjax;

$this->title = 'QR Code Generator';
$this->params['breadcrumbs'][] = 'Tools';
$this->params['breadcrumbs'][] = $this->title;

TimePicker::widget();

echo Html::tag('h1', $this->title);

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-lg-8 mx-auto']);
		if ($qr = Yii::$app->session->getFlash('qr-success')) :
			$imgSize = min(250, $model->size);
			Alert::begin(['options' => ['class' => 'alert-success fade show clearfix']]);
				echo Html::img(Url::to('@assets/temp/'.basename($qr)), ['alt' => $model->type.' QR Code', 'class' => 'float-left mr-2', 'height' => $imgSize, 'width' => $imgSize]);
				echo Html::tag('div', 'Your QR Code has been generated successfully.');
				echo Html::tag('div', 'Do not link to the image on this website directly as it will be deleted shortly.');
			Alert::end();
		endif;

		if ($size = Yii::$app->session->getFlash('qr-size')) :
			Alert::begin(['options' => ['class' => 'alert-danger']]);
				echo 'Too much information: Try to decrease the size by '.$size.' characters.';
			Alert::end();
		endif;

		$form = ActiveForm::begin();
		echo $form->field($model, 'type', [
				'template' => '{label}<div class="input-group">'.Icon::fieldAddon('th-list').'{input}</div>{error}',
			])->dropDownList(Qr::getTypes(), [
				'prompt' => 'Select a Type',
				'onchange'=> new JsExpression("if(!this.value){\$('#form').empty()}else{\$('#form').load('',{'type':this.value})}"),
				'tabindex' => 1,
			]);
		ActiveForm::end();

		Pjax::begin(['id' => 'form']);
		if (Yii::$app->request->isPost) :
			echo $qrForm;
		endif;
		Pjax::end();
	echo Html::endTag('div');
echo Html::endTag('div');
