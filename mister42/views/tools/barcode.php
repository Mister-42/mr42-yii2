<?php
use app\models\tools\Barcode;
use yii\bootstrap4\{ActiveForm, Alert, Html};
use yii\helpers\Url;

$this->title = Yii::t('mr42', 'Barcode Generator');
$this->params['breadcrumbs'][] = Yii::t('mr42', 'Tools');
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', $this->title);

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-12 col-lg-8 mx-auto']);
		if ($barcode = Yii::$app->session->getFlash('barcode-success')) :
			list($width, $height) = getimagesize($barcode);
			$imgHeight = min(150, $height);
			$imgWidth = round($imgHeight / $height * $width);
			Alert::begin(['options' => ['class' => 'alert-success fade show clearfix']]);
				echo Html::img(Url::to('@assets/temp/'.basename($barcode)), ['alt' => Yii::t('mr42', 'Barcode'), 'class' => 'float-left mr-2', 'height' => $imgHeight, 'width' => $imgWidth]);
				echo Html::tag('div', Yii::t('mr42', 'Your Barcode has been generated successfully.'));
				echo Html::tag('div', Yii::t('mr42', 'Do not link to the image on this website directly as it will be deleted shortly.'));
			Alert::end();
		endif;

		$form = ActiveForm::begin();
		$tab = 0;

		echo $form->field($model, 'type', [
				'template' => '{label}<div class="input-group">'.Yii::$app->icon->fieldAddon('th-list').'{input}</div>{error}',
			])->dropDownList(Barcode::getTypes(), [
				'prompt' => Yii::t('mr42', 'Select a Type'),
				'tabindex' => ++$tab,
			]);

		echo $form->field($model, 'code', [
				'template' => '{label}<div class="input-group">'.Yii::$app->icon->fieldAddon('barcode').'{input}</div>{error}',
			])->input('number', ['tabindex' => ++$tab]);

		echo Html::tag('div',
			$form->field($model, 'height', [
				'options' => ['class' => 'col-sm-6'],
				'template' => '{label}<div class="input-group">'.Yii::$app->icon->fieldAddon('arrows-alt-v').'{input}</div>{error}',
			])->input('number', ['tabindex' => ++$tab]).
			$form->field($model, 'barWidth', [
				'options' => ['class' => 'col-sm-6'],
				'template' => '{label}<div class="input-group">'.Yii::$app->icon->fieldAddon('arrows-alt-h').'{input}</div>{hint} {error}',
			])->input('number', ['step' => '0.5', 'tabindex' => ++$tab])
		, ['class' => 'row form-group']);

		echo $form->field($model, 'recipient', [
				'template' => '{label} '.Yii::t('mr42', '(optional)').'<div class="input-group">'.Yii::$app->icon->fieldAddon('at').'{input}</div>{hint} {error}',
			])->input('email', ['tabindex' => ++$tab])
			->hint(Yii::t('mr42', 'If you enter your email address the image will be mailed to that address.'));

		echo Html::tag('div',
			Html::resetButton(Yii::t('mr42', 'Reset'), ['class' => 'btn btn-default ml-1', 'tabindex' => $tab + 2]).
			Html::submitButton(Yii::t('mr42', 'Generate Barcode'), ['class' => 'btn btn-primary ml-1', 'tabindex' => ++$tab])
		, ['class' => 'btn-toolbar float-right form-group']);

		ActiveForm::end();
	echo Html::endTag('div');
echo Html::endTag('div');
