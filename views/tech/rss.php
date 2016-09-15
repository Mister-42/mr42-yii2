<?php
use Yii;
use app\models\General;
use app\models\post\Post;
use yii\caching\DbDependency;
use yii\helpers\Html;
use yii\helpers\Url;

$dependency = [
	'class' => DbDependency::className(),
	'reusable' => true,
	'sql' => 'SELECT MAX(updated) FROM '.Post::tableName().' WHERE `active` = '.Post::STATUS_ACTIVE.';'
];

if ($this->beginCache('feedRss', ['dependency' => $dependency, 'duration' => 0])) {
	$doc=new DOMDocument('1.0', 'UTF-8');
	$doc->formatOutput = YII_ENV_DEV;

	$rss = $doc->createElement('rss');
	$rss->setAttribute('version', '2.0');
	$rss->setAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom');
	$rss->setAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
	$doc->appendChild($rss);

	$channel = $doc->createElement('channel');
	$channel->appendChild($doc->createElement('title', Html::encode(Yii::$app->name)));
	$channel->appendChild($doc->createElement('link', Url::to(['site/index'], true)));
	$channel->appendChild($doc->createElement('description', Html::encode(Yii::$app->params['description'])));
		$atomSelfLink = $doc->createElement('atom:link');
		$atomSelfLink->setAttribute('href', Url::to(['tech/rss'], true));
		$atomSelfLink->setAttribute('rel', 'self');
		$atomSelfLink->setAttribute('type', 'application/rss+xml');
	$channel->appendChild($atomSelfLink);
	$channel->appendChild($doc->createElement('language', Html::encode(Yii::$app->language)));
	$channel->appendChild($doc->createElement('copyright', '&#169; 2014-'.date('Y').' '.Html::encode(Yii::$app->name)));
	$channel->appendChild($doc->createElement('pubDate', date(DATE_RSS)));
	$channel->appendChild($doc->createElement('lastBuildDate', date(DATE_RSS, $posts[0]->updated)));
		$rssImage = $doc->createElement('image');
		$rssImage->appendChild($doc->createElement('title', Html::encode(Yii::$app->name)));
		$rssImage->appendChild($doc->createElement('url', Url::to(Yii::$app->assetManager->baseUrl.'/images/logo.png', Yii::$app->request->isSecureConnection ? 'https' : 'http')));
		$rssImage->appendChild($doc->createElement('link', Url::to(['site/index'], true)));
		$rssImage->appendChild($doc->createElement('description', Html::encode(Yii::$app->params['description'])));
		list($width, $height, $type, $attr) = getimagesize(Yii::$app->assetManager->basePath.'/images/logo.png');
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
	$this->endCache();
}
