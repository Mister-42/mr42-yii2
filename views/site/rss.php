<?php
use Yii;
use app\models\General;
use app\models\post\Post;
use yii\bootstrap\Html;
use yii\caching\DbDependency;
use yii\helpers\Url;

$doc=new DOMDocument('1.0', 'UTF-8');
$doc->formatOutput = YII_ENV_DEV;

$rss = $doc->createElement('rss');
$rss->setAttribute('version', '2.0');
$rss->setAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom');
$rss->setAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
$doc->appendChild($rss);

$channel = $doc->createElement('channel');
$channel->appendChild($doc->createElement('title', Html::encode(Yii::$app->name)));
$channel->appendChild($doc->createElement('link', Url::home(true)));
$channel->appendChild($doc->createElement('description', Html::encode(Yii::$app->params['description'])));
	$atomSelfLink = $doc->createElement('atom:link');
	$atomSelfLink->setAttribute('href', Url::to(['site/rss'], true));
	$atomSelfLink->setAttribute('rel', 'self');
	$atomSelfLink->setAttribute('type', 'application/rss+xml');
$channel->appendChild($atomSelfLink);
$channel->appendChild($doc->createElement('language', Html::encode(Yii::$app->language)));
$channel->appendChild($doc->createElement('copyright', '&#169; 2014-'.date('Y').' '.Html::encode(Yii::$app->name)));
$channel->appendChild($doc->createElement('pubDate', date(DATE_RSS)));
$channel->appendChild($doc->createElement('lastBuildDate', date(DATE_RSS, $posts[0]->updated)));
	$rssImage = $doc->createElement('image');
	$rssImage->appendChild($doc->createElement('title', Html::encode(Yii::$app->name)));
	$rssImage->appendChild($doc->createElement('url', Url::to(Yii::$app->assetManager->getBundle('app\assets\ImagesAsset')->baseUrl.'/logo.png', Yii::$app->request->isSecureConnection ? 'https' : 'http')));
	$rssImage->appendChild($doc->createElement('link', Url::home(true)));
	$rssImage->appendChild($doc->createElement('description', Html::encode(Yii::$app->params['description'])));
	list($width, $height, $type, $attr) = getimagesize(Yii::$app->assetManager->getBundle('app\assets\ImagesAsset')->basePath.'/logo.png');
	$rssImage->appendChild($doc->createElement('height', $height));
	$rssImage->appendChild($doc->createElement('width', $width));
$channel->appendChild($rssImage);
$rss->appendChild($channel);

foreach($posts as $post) {
	if (strpos($post->content, '[readmore]')) {
		$post->content = substr($post->content, 0, strpos($post->content, '[readmore]'));
		$post->content .= Html::a('Read full article on our website', Url::to(['post/index', 'id'=>$post->id, 'title'=>$post->title], true)).' &raquo;';
	}

	$item = $doc->createElement('item');
	$item->appendChild($doc->createElement('title', $post->title));
	$item->appendChild($doc->createElement('link', Html::encode(Url::to(['post/index', 'id'=>$post->id, 'title'=>$post->title], true))));
		$description = $doc->createElement('description');
		$description->appendChild($doc->createCDATASection(General::cleanInput($post->content, 'gfm')));
	$item->appendChild($description);
	$item->appendChild($doc->createElement('dc:creator', $post->user->username));
	$item->appendChild($doc->createElement('category', Html::encode($post->tags)));
		$guid = $doc->createElement('guid', Html::encode(Url::to(['post/index', 'id'=>$post->id], true)));
		$guid->setAttribute('isPermaLink', 'true');
	$item->appendChild($guid);
	$item->appendChild($doc->createElement('pubDate', date(DATE_RSS, $post->created)));
	$channel->appendChild($item);
}

echo $doc->saveXML();
