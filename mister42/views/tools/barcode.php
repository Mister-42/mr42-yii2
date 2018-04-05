<?php
use app\models\tools\Barcode;
use yii\bootstrap\{ActiveForm, Alert, Html};
use yii\helpers\Url;

$this->title = 'Barcode Generator';
$this->params['breadcrumbs'][] = 'Tools';
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', Html::encode($this->title));

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-offset-2 col-md-8']);
		if ($barcode = Yii::$app->session->getFlash('barcode-success')) {
			list($width, $height) = getimagesize($barcode);
			$imgHeight = min(150, $height);
			$imgWidth = round($imgHeight / $height * $width);
			Alert::begin(['options' => ['class' => 'alert-success', 'style' => ['min-height' => $imgHeight + 30 . 'px']]]);
			echo Html::img(Url::to('@assets/temp/'.basename($barcode)), ['alt' => 'Barcode', 'class' => 'img-responsive inline-left pull-left', 'height' => $imgHeight, 'width' => $imgWidth]);
			echo Html::tag('p', 'Your Barcode has been generated successfully.');
			echo Html::tag('p', 'Do not link to the image on this website directly as it will be deleted shortly.');
			Alert::end();
		}

		$form = ActiveForm::begin();

		echo $form->field($model, 'type', [
				'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('th-list').'</span>{input}</div>{error}',
			])->dropDownList(Barcode::getTypes(), [
				'prompt' => 'Select a Type',
				'tabindex' => 1,
			]);

		echo $form->field($model, 'code', [
				'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('barcode').'</span>{input}</div>{error}',
			])->input('number', ['tabindex' => 2]);

		echo Html::tag('div',
			$form->field($model, 'height', [
				'options' => ['class' => 'col-sm-6'],
				'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('resize-vertical').'</span>{input}</div>{error}',
			])->input('number', ['tabindex' => 3]) .
			$form->field($model, 'barWidth', [
				'options' => ['class' => 'col-sm-6'],
				'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('resize-horizontal').'</span>{input}</div>{hint} {error}',
			])->input('number', ['step' => '0.5', 'tabindex' => 4])
		, ['class' => 'row']);

		echo $form->field($model, 'recipient', [
				'template' => '{label} (optional)<div class="input-group"><span class="input-group-addon">'.Html::icon('envelope').'</span>{input}</div>{hint} {error}',
			])->input('email', ['tabindex' => 5])
			->hint('If you enter your email address here the Barcode will be mailed to that address.');

		echo Html::tag('div',
			Html::resetButton('Reset', ['class' => 'btn btn-default', 'tabindex' => 7]) .
			Html::submitButton('Generate Barcode', ['class' => 'btn btn-primary', 'tabindex' => 6])
		, ['class' => 'btn-toolbar form-group field-qr-buttons pull-right']);

		ActiveForm::end();
	echo Html::endTag('div');
echo Html::endTag('div');
