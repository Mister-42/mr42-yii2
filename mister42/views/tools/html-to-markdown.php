<?php
use app\assets\Html2MarkdownAsset;
use app\models\articles\Articles;
use yii\bootstrap4\Html;

$this->title = Yii::t('mr42', 'HTML to Markdown Converter');
$this->params['breadcrumbs'] = [Yii::t('mr42', 'Tools')];
$this->params['breadcrumbs'][] = $this->title;

$lastPost = Articles::findOne(['id' => 4]);

Html2MarkdownAsset::register($this);

echo Html::tag('h1', $this->title);

echo Html::beginTag('form', ['class' => 'html2markdown']);
	echo Html::beginTag('div', ['class' => 'row']);
		echo Html::tag('div',
			Html::tag('h3', Yii::t('mr42', 'HTML')).
			Html::textArea('input', $lastPost->contentParsed)
		, ['class' => 'col']);
		echo Html::tag('div',
			Html::tag('h3', Yii::t('mr42', 'Markdown')).
			Html::textArea('output', Yii::t('mr42', 'JavaScript is disabled in your web browser. This tool does not work without JavaScript.'), ['readonly' => true])
		, ['class' => 'col']);
	echo Html::endTag('div');

	echo Html::beginTag('div', ['class' => 'row']);
		echo Html::tag('div',
			Html::checkbox('gfm', false, ['label' => Yii::$app->icon->show('github', ['class' => 'mr-1', 'style' => 'brands']).Yii::t('mr42', 'GitHub Flavored Markdown')])
		, ['class' => 'col']);
	echo Html::endTag('div');
echo Html::endTag('form');
