<?php
use app\assets\Html2MarkdownAsset;
use yii\bootstrap\Html;

$this->title = 'HTML to Markdown Converter';
$this->params['breadcrumbs'][] = 'Tools';
$this->params['breadcrumbs'][] = $this->title;

Html2MarkdownAsset::register($this);

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::tag('h1', Html::encode($this->title), ['class' => 'col-md-12']);
echo Html::endTag('div');

echo Html::beginTag('form', ['class' => 'html2markdown']);
	echo Html::beginTag('div', ['class' => 'row']);
		echo Html::tag('div',
			Html::tag('h3', 'HTML') .
			Html::textArea('input', $lastPost->content)
		, ['class' => 'col-md-6']);
		echo Html::tag('div',
			Html::tag('h3', 'Markdown') .
			Html::textArea('output', 'JavaScript is disabled in your web browser. This tool does not work without JavaScript.', ['readonly' => true])
		, ['class' => 'col-md-6']);
	echo Html::endTag('div');

	echo Html::beginTag('div', ['class' => 'row']);
		echo Html::tag('div',
			Html::label(Html::checkbox('gfm') . ' GitHub Flavored Markdown')
		, ['class' => 'col-md-12']);
	echo Html::endTag('div');
echo Html::endTag('form');
