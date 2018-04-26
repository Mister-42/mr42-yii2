<?php
use app\assets\Html2MarkdownAsset;
use yii\bootstrap4\Html;

$this->title = 'HTML to Markdown Converter';
$this->params['breadcrumbs'][] = 'Tools';
$this->params['breadcrumbs'][] = $this->title;

Html2MarkdownAsset::register($this);

echo Html::tag('h1', $this->title);

echo Html::beginTag('form', ['class' => 'html2markdown']);
	echo Html::beginTag('div', ['class' => 'row']);
		echo Html::tag('div',
			Html::tag('h3', 'HTML') .
			Html::textArea('input', $lastPost->content)
		, ['class' => 'col']);
		echo Html::tag('div',
			Html::tag('h3', 'Markdown') .
			Html::textArea('output', 'JavaScript is disabled in your web browser. This tool does not work without JavaScript.', ['readonly' => true])
		, ['class' => 'col']);
	echo Html::endTag('div');

	echo Html::beginTag('div', ['class' => 'row']);
		echo Html::tag('div',
			Html::label(Html::checkbox('gfm') . ' GitHub Flavored Markdown')
		, ['class' => 'col']);
	echo Html::endTag('div');
echo Html::endTag('form');
