<?php
use app\models\ActiveForm;
use app\models\tools\Barcode;
use yii\bootstrap4\{Alert, Html};
use yii\helpers\Url;

$this->title = Yii::t('mr42', 'Barcode Generator');
$this->params['breadcrumbs'][] = Yii::t('mr42', 'Tools');
$this->params['breadcrumbs'][] = $this->title;

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-12 col-lg-8 mx-auto']);
		echo Html::tag('h1', $this->title);
		if ($barcode = Yii::$app->session->getFlash('barcode-success')) :
			[$width, $height] = getimagesize($barcode);
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
			'icon' => 'th-list',
		])->dropDownList(Barcode::getTypes(), [
			'prompt' => Yii::t('mr42', 'Select a Type'),
			'tabindex' => ++$tab,
		]);

		echo $form->field($model, 'code', [
			'icon' => 'barcode',
		])->input('number', ['tabindex' => ++$tab]);

		echo Html::tag('div',
			$form->field($model, 'height', [
				'icon' => 'arrows-alt-v',
				'options' => ['class' => 'col-sm-6'],
			])->input('number', ['tabindex' => ++$tab]).
			$form->field($model, 'barWidth', [
				'icon' => 'arrows-alt-h',
				'options' => ['class' => 'col-sm-6'],
			])->input('number', ['step' => '0.5', 'tabindex' => ++$tab])
		, ['class' => 'row form-group']);

		echo $form->field($model, 'recipient', [
			'icon' => 'at',
			'template' => '{label} '.Yii::t('mr42', '(optional)').'{input}{hint} {error}',
		])->input('email', ['tabindex' => ++$tab])
		->hint(Yii::t('mr42', 'If you enter your email address the image will be mailed to that address.'));

		echo $form->submitToolbar(Yii::t('mr42', 'Generate Barcode'), $tab);

		ActiveForm::end();
	echo Html::endTag('div');
echo Html::endTag('div');
