<?php
use app\models\Icon;
use app\models\tools\Barcode;
use yii\bootstrap4\{ActiveForm, Alert, Html};
use yii\helpers\Url;

$this->title = 'Barcode Generator';
$this->params['breadcrumbs'][] = 'Tools';
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', $this->title);

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-12 col-lg-8 mx-auto']);
		if ($barcode = Yii::$app->session->getFlash('barcode-success')) {
			list($width, $height) = getimagesize($barcode);
			$imgHeight = min(150, $height);
			$imgWidth = round($imgHeight / $height * $width);
			Alert::begin(['options' => ['class' => 'alert-success clearfix']]);
			echo Html::img(Url::to('@assets/temp/'.basename($barcode)), ['alt' => 'Barcode', 'class' => 'float-left mr-2', 'height' => $imgHeight, 'width' => $imgWidth]);
			echo Html::tag('div', 'Your Barcode has been generated successfully.');
			echo Html::tag('div', 'Do not link to the image on this website directly as it will be deleted shortly.');
			Alert::end();
		}

		$form = ActiveForm::begin();

		echo $form->field($model, 'type', [
				'template' => '{label}<div class="input-group">'.Icon::fieldAddon('th-list').'{input}</div>{error}',
			])->dropDownList(Barcode::getTypes(), [
				'prompt' => 'Select a Type',
				'tabindex' => ++$tab,
			]);

		echo $form->field($model, 'code', [
				'template' => '{label}<div class="input-group">'.Icon::fieldAddon('barcode').'{input}</div>{error}',
			])->input('number', ['tabindex' => ++$tab]);

		echo Html::tag('div',
			$form->field($model, 'height', [
				'options' => ['class' => 'col-sm-6'],
				'template' => '{label}<div class="input-group">'.Icon::fieldAddon('arrows-alt-v').'{input}</div>{error}',
			])->input('number', ['tabindex' => ++$tab]) .
			$form->field($model, 'barWidth', [
				'options' => ['class' => 'col-sm-6'],
				'template' => '{label}<div class="input-group">'.Icon::fieldAddon('arrows-alt-h').'{input}</div>{hint} {error}',
			])->input('number', ['step' => '0.5', 'tabindex' => ++$tab])
		, ['class' => 'row']);

		echo $form->field($model, 'recipient', [
				'template' => '{label} (optional)<div class="input-group">'.Icon::fieldAddon('at').'{input}</div>{hint} {error}',
			])->input('email', ['tabindex' => ++$tab])
			->hint('If you enter your email address the barcode will be mailed to that address.');

		echo Html::tag('div',
			Html::resetButton('Reset', ['class' => 'btn btn-default ml-1', 'tabindex' => $tab+2]) .
			Html::submitButton('Generate Barcode', ['class' => 'btn btn-primary ml-1', 'tabindex' => ++$tab])
		, ['class' => 'btn-toolbar float-right form-group']);

		ActiveForm::end();
	echo Html::endTag('div');
echo Html::endTag('div');
