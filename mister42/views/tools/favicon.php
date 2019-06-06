<?php
use app\assets\HighlightAsset;
use app\models\Form;
use yii\bootstrap4\{ActiveForm, Alert, Html};
use yii\helpers\{Inflector, Url};
use yii\web\View;

$this->title = Yii::t('mr42', 'Favicon Converter');
$this->params['breadcrumbs'][] = Yii::t('mr42', 'Tools');
$this->params['breadcrumbs'][] = $this->title;

HighlightAsset::register($this);
$this->registerJs("var inputFile = {lang:{selected:'".Yii::t('mr42', 'File {name} Selected', ['name' => Html::tag('span', null, ['class' => 'filename'])])."'}};".Yii::$app->formatter->jspack('inputFile.js'), View::POS_READY);

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-12 col-lg-8 mx-auto']);
		echo Html::tag('h1', $this->title);
		echo Html::tag('div', Yii::t('mr42', 'A favicon (short for \'favorites icon\'), are little icons associated with a particular website, shown next to the site\'s name in the URL bar or the page\'s title on the tab of all major browsers.'), ['class' => 'alert alert-info']);

		foreach ($model->dimensions as $dimension)
			$dimensions[] = $dimension.'x'.$dimension;

		if ($flash = Yii::$app->session->getFlash('favicon-error')) :
			echo Alert::widget(['options' => ['class' => 'alert-danger fade show'], 'body' => $flash]);
		endif;

		if ($icon = Yii::$app->session->getFlash('favicon-success')) :
			Alert::begin(['options' => ['class' => 'alert-success clearfix']]);
				echo Html::img(Url::to('@assets/temp/'.$icon), ['alt' => 'favicon.ico', 'class' => 'float-left mr-2', 'height' => 64, 'width' => 64]);
				echo Html::tag('div', Yii::t('mr42', 'Your icon has been generated successfully. Save it to your website and add the code below between the &lt;head&gt; tags of your html. This will allow all major browsers to show the icon when the website is accessed and/or bookmarked.'));
				echo Html::tag('div', Yii::t('mr42', 'Do not link to the image on this website directly as it will be deleted shortly.'));
				echo Html::tag('pre',
					Html::tag('code', '&lt;link rel="icon" href="/path/to/'.$icon.'" type="image/x-icon" sizes="'.implode(' ', $dimensions).'" /&gt;')
				);
			Alert::end();
		endif;

		$form = ActiveForm::begin();
		$tab = 0;

		echo $form->field($model, 'recipient', [
				'template' => '{label} '.Yii::t('mr42', '(optional)').'<div class="input-group">'.Yii::$app->icon->fieldAddon('at').'{input}</div>{hint} {error}',
			])->input('email', ['tabindex' => ++$tab])
			->hint(Yii::t('mr42', 'If you enter your email address the image will be mailed to that address.'));

		echo $form->field($model, 'sourceImage', [
				'template' => Html::tag('label', $model->getAttributeLabel('sourceImage'), ['for' => 'sourceFile']).'<div class="input-group">'.Yii::$app->icon->fieldAddon('image').'<div class="custom-file">{label}{input}</div></div>{hint} {error}',
			])->fileInput(['accept' => 'image/*', 'class' => 'custom-file-input', 'id' => 'sourceFile', 'tabindex' => ++$tab])
			->hint(Yii::t('mr42', 'For best result upload a square image. Your icon will be generated in {dimensions} pixels.', ['dimensions' => Inflector::sentence($dimensions)]))
			->label(Yii::t('mr42', 'Select an Image'), ['class' => 'custom-file-label text-truncate']);

		echo Form::submitToolbar(Yii::t('mr42', 'Convert Image'), $tab);

		ActiveForm::end();
	echo Html::endTag('div');
echo Html::endTag('div');
