<?php
use nezhelskoy\highlight\HighlightAsset;
use yii\bootstrap\{ActiveForm, Alert, Html};
use yii\helpers\{Inflector, Url};
use yii\web\View;

$this->title = 'Favicon Converter';
$this->params['breadcrumbs'][] = 'Tools';
$this->params['breadcrumbs'][] = $this->title;

HighlightAsset::register($this);
$this->registerJs(Yii::$app->formatter->jspack('inputFile.js'), View::POS_READY);

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-offset-2 col-md-8']);
		echo Html::tag('h1', Html::encode($this->title));
		echo Html::tag('div', 'A favicon (short for \'favorites icon\'), are little icons associated with a particular website, shown next to the site\'s name in the URL bar or the page\'s title on the tab of all major browsers.', ['class' => 'alert alert-info']);

		foreach ($model->dimensions as $dimension)
			$dimensions[] = $dimension.'x'.$dimension;

		if ($flash = Yii::$app->session->getFlash('favicon-error'))
			echo Alert::widget(['options' => ['class' => 'alert-danger'], 'body' => $flash]);

		if ($icon = Yii::$app->session->getFlash('favicon-success')) {
			Alert::begin(['options' => ['class' => 'alert-success']]);
				echo Html::img(Url::to('@assets/temp/'.$icon), ['alt' => 'favicon.ico', 'class' => 'inline-left pull-left', 'height' => 64, 'width' => 64]);
				echo '<p>Your icon has been generated successfully. Save it to your website and add the code below between the &lt;head&gt; tags of your html. This will allow all major browsers to show the icon when the website is accessed and/or bookmarked.<br>';
				echo 'Do not link to the icon on this website directly as it will be deleted shortly.</p>';
				echo '<br><pre><code>&lt;link rel="icon" href="/path/to/'.$icon.'" type="image/x-icon" sizes="'.implode(' ', $dimensions).'" /&gt;</code></pre>';
			Alert::end();
		}

		$form = ActiveForm::begin();

		echo $form->field($model, 'email', [
				'template' => '{label} (optional)<div class="input-group"><span class="input-group-addon">'.Html::icon('envelope').'</span>{input}</div>{hint} {error}',
			])->input('email', ['tabindex' => 1])
			->hint('If you enter your email address here the favicon will be mailed to that address.');

		echo Html::beginTag('div', ['class' => 'input-group']);
			echo Html::tag('span', Html::icon('picture'), ['class' => 'input-group-addon']);
			echo Html::textInput('file', null, ['class' => 'form-control', 'id' => 'file', 'placeholder' => 'Select an image', 'onclick' => "$('input[id=sourceFile]')", 'readonly' => true]);
			echo Html::tag('span',
				Html::button(Html::icon('folder-open'), ['class' => 'btn btn-primary', 'onclick' => "$('input[id=sourceFile]').click()", 'tabindex' => 2])
			, ['class' => 'input-group-btn']);
		echo Html::endTag('div');

		echo $form->field($model, 'sourceImage')
			->fileInput(['accept' => 'image/*', 'class' => 'hidden', 'id' => 'sourceFile'])
			->hint('For the best result you should upload a square image. Your icon will be generated in ' . Inflector::sentence($dimensions) . ' pixels.')
			->label(false);

		echo Html::tag('div',
			Html::resetButton('Reset', ['class' => 'btn btn-default', 'tabindex' => 4]) .
			Html::submitButton($model->getAttributeLabel('generate'), ['class' => 'btn btn-primary', 'tabindex' => 3])
		, ['class' => 'btn-toolbar form-group pull-right']);

		ActiveForm::end();
	echo Html::endTag('div');
echo Html::endTag('div');
