<?php
use app\models\tools\Qr;
use janisto\timepicker\TimePicker;
use yii\bootstrap\{ActiveForm, Alert, Html};
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\Pjax;

$this->title = 'QR Code Generator';
$this->params['breadcrumbs'][] = 'Tools';
$this->params['breadcrumbs'][] = $this->title;

TimePicker::widget();

echo Html::tag('h1', Html::encode($this->title));

echo '<div class="row">';
	echo '<div class="col-md-offset-2 col-md-8">';
		if ($qr = Yii::$app->session->getFlash('qr-success')) {
			$imgSize = min(250, $model->size);
			Alert::begin(['options' => ['class' => 'alert-success', 'style' => ['min-height' => $imgSize + 30 . 'px']]]);
				echo Html::img(Url::to('@assets/temp/'.$qr), ['alt' => $model->type . ' QR Code', 'class' => 'inline-left pull-left', 'height' => $imgSize, 'width' => $imgSize]);
				echo Html::tag('p', 'Your QR Code has been generated successfully.');
				echo Html::tag('p', 'Do not link to the image on this website directly. Your image will be deleted shortly.');
			Alert::end();
		}

		if ($size = Yii::$app->session->getFlash('qr-size')) {
			Alert::begin(['options' => ['class' => 'alert-danger']]);
				echo 'Too much information: Try to decrease the size by ' . $size . ' characters.';
			Alert::end();
		}

		$form = ActiveForm::begin();
		echo $form->field($model, 'type', [
				'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('th-list').'</span>{input}</div>{error}',
			])->dropDownList(Qr::getTypes(), [
				'prompt' => 'Select a Type',
				'onchange'=> new JsExpression("if(!this.value){\$('#form').empty()}else{\$('#form').load('',{'type':this.value})}"),
				'tabindex' => 1,
			]);
		ActiveForm::end();

		Pjax::begin(['id' => 'form']);
		if (Yii::$app->request->isPost)
			echo $qrForm;
		Pjax::end();
	echo '</div>';
echo '</div>';
