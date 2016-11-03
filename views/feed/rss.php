<?php
use Yii;
use yii\bootstrap\Html;
use yii\helpers\{StringHelper, Url};

$doc = new XMLWriter();
$doc->openMemory();
$doc->setIndent(YII_ENV_DEV);

$doc->startDocument('1.0', 'UTF-8');
$doc->startElement('rss');
$doc->writeAttribute('version', '2.0');
$doc->writeAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom');
$doc->writeAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');

$doc->startElement('channel');
$doc->writeElement('title', Html::encode(Yii::$app->name));
$doc->writeElement('link', Url::home(true));
$doc->writeElement('description', Html::encode(Yii::$app->params['description']));
	$doc->startElement('atom:link');
	$doc->writeAttribute('href', Url::to(['feed/rss'], true));
	$doc->writeAttribute('rel', 'self');
	$doc->writeAttribute('type', 'application/rss+xml');
	$doc->endElement();
$doc->writeElement('language', Html::encode(Yii::$app->language));
$doc->writeElement('copyright', '2014-'.date('Y').' '.Html::encode(Yii::$app->name));
$doc->writeElement('pubDate', date(DATE_RSS));
$doc->writeElement('lastBuildDate', date(DATE_RSS, $articles[0]->updated));
	$doc->startElement('image');
	$doc->writeElement('title', Html::encode(Yii::$app->name));
	$doc->writeElement('url', Url::to(Yii::$app->assetManager->getBundle('app\assets\ImagesAsset')->baseUrl.'/logo.png', Yii::$app->request->isSecureConnection ? 'https' : 'http'));
	$doc->writeElement('link', Url::home(true));
	$doc->writeElement('description', Html::encode(Yii::$app->params['description']));
	list($width, $height, $type, $attr) = getimagesize(Yii::$app->assetManager->getBundle('app\assets\ImagesAsset')->basePath.'/logo.png');
	$doc->writeElement('height', $height);
	$doc->writeElement('width', $width);
	$doc->endElement();

foreach($articles as $article) :
	if (strpos($article->content, '[readmore]')) {
		$article->content = substr($article->content, 0, strpos($article->content, '[readmore]'));
		$article->content .= Html::a('Read full article on our website', Url::to(['articles/index', 'id' => $article->id, 'title' => $article->url], true)).' &raquo;';
	}

	$doc->startElement('item');
	$doc->writeElement('title', $article->title);
	$doc->writeElement('link', Html::encode(Url::to(['articles/index', 'id' => $article->id, 'title' => $article->url], true)));
		$doc->startElement('description');
		$doc->writeCData($article->content);
		$doc->endElement();
	$doc->writeElement('dc:creator', $article->user->username);
	foreach (StringHelper::explode($article->tags) as $tag) $doc->writeElement('category', Html::encode($tag));
		$doc->startElement('guid');
		$doc->writeAttribute('isPermaLink', 'true');
		$doc->text(Html::encode(Url::to(['articles/index', 'id' => $article->id], true)));
		$doc->endElement();
	$doc->writeElement('pubDate', date(DATE_RSS, $article->created));
	$doc->endElement();
endforeach;

$doc->endElement();
$doc->endDocument();
echo $doc->outputMemory();
