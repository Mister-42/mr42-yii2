<?php
use yii\bootstrap4\Html;
use yii\helpers\{StringHelper, Url};

$doc = new XMLWriter();
$doc->openMemory();
$doc->setIndent(YII_DEBUG && php_sapi_name() !== 'cli' && (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin));

$doc->startDocument('1.0', 'UTF-8');
$doc->startElement('rss');
$doc->writeAttribute('version', '2.0');
$doc->writeAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom');
$doc->writeAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');

$doc->startElement('channel');
$doc->writeElement('title', Yii::$app->name);
$doc->writeElement('link', Yii::$app->params['shortDomain']);
$doc->writeElement('description', Yii::$app->params['description']);
	$doc->startElement('atom:link');
	$doc->writeAttribute('href', Url::to(['feed/rss'], true));
	$doc->writeAttribute('rel', 'self');
	$doc->writeAttribute('type', 'application/rss+xml');
	$doc->endElement();
$doc->writeElement('language', Yii::$app->language);
$doc->writeElement('copyright', '2014-'.date('Y').' '.Yii::$app->name);
$doc->writeElement('pubDate', date(DATE_RSS));
$doc->writeElement('lastBuildDate', date(DATE_RSS, $articles[0]->updated));
	$doc->startElement('image');
	$doc->writeElement('title', Yii::$app->name);
	$doc->writeElement('url', Url::to('@assets/images/mr42.png', Yii::$app->request->isSecureConnection ? 'https' : 'http'));
	$doc->writeElement('link', Yii::$app->params['shortDomain']);
	$doc->writeElement('description', Yii::$app->params['description']);
	list($width, $height, $type, $attr) = getimagesize(Yii::getAlias('@assetsroot/images/mr42.png'));
	$doc->writeElement('height', $height);
	$doc->writeElement('width', $width);
	$doc->endElement();

foreach ($articles as $article) :
	$article->content = preg_replace('/<img([^>]*)src=["]([^"]*)["]([^>]*)>/', '<img$1src="https:$2"$3>', $article->content);
	if (strpos($article->content, '[readmore]')) :
		$article->content = substr($article->content, 0, strpos($article->content, '[readmore]'));
		$article->content .= Html::a('Read Full Article', Url::to(['articles/index', 'id' => $article->id, 'title' => $article->url], true)).' &raquo;';
	endif;

	$doc->startElement('item');
	$doc->writeElement('title', $article->title);
	$doc->writeElement('link', Url::to(['articles/index', 'id' => $article->id, 'title' => $article->url], true));
		$doc->startElement('description');
		$doc->writeCData($article->content);
		$doc->endElement();
	$doc->writeElement('dc:creator', $article->user->username);
	foreach (StringHelper::explode($article->tags) as $tag) :
		$doc->startElement('category');
		$doc->writeAttribute('domain', Url::to(['articles/index', 'action' => 'tag', 'q' => $tag], true));
		$doc->text($tag);
		$doc->endElement();
	endforeach;
		$doc->startElement('guid');
		$doc->writeAttribute('isPermaLink', 'true');
		$doc->text(Url::to(['permalink/articles', 'id' => $article->id]));
		$doc->endElement();
	$doc->writeElement('pubDate', date(DATE_RSS, $article->created));
	if ($article->source) :
		$doc->startElement('source');
		$doc->writeAttribute('url', $article->source);
		$doc->text('Source');
		$doc->endElement();
	endif;
	$doc->endElement();
endforeach;

$doc->endElement();
$doc->endDocument();
echo $doc->outputMemory();
