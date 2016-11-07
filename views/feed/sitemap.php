<?php
use app\models\articles\Tags;
use app\models\feed\Sitemap;
use app\models\lyrics\{Lyrics1Artists, Lyrics2Albums, Lyrics3Tracks};
use yii\base\View;
use yii\helpers\Url;

$doc = new XMLWriter();
$doc->openMemory();
$doc->setIndent(YII_ENV_DEV);

$doc->startDocument('1.0', 'UTF-8');
$doc->startElement('urlset');
$doc->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
$doc->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
$doc->writeAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');

Sitemap::prioData($doc, Url::home(true), 1, filemtime(View::findViewFile('@app/views/site/index')));

foreach($pages as $page)
	Sitemap::ageData($doc, Url::to([$page], true), filemtime(View::findViewFile('@app/views' . $page)));

Sitemap::ageData($doc, Url::to(['articles/index'], true), end($articles)->updated, 0.8);

foreach($articles as $article) :
	$lastUpdate = $article['updated'];
	foreach ($article['comments'] as $comment)
		$lastUpdate = max($lastUpdate, $comment['created']);
	Sitemap::ageData($doc, Url::to(['articles/index', 'id' => $article['id'], 'title' => $article['url']], true), $lastUpdate);
endforeach;

foreach($tags as $tag => $value) :
	$lastUpdate = Tags::lastUpdate($tag);
	Sitemap::prioData($doc, Url::to(['articles/index', 'action' => 'tag', 'tag' => $tag], true), $tags[$tag] / max($tags) - 0.2, $lastUpdate);
endforeach;

Sitemap::ageData($doc, Url::to(['lyrics/index'], true), Lyrics1Artists::lastUpdate(null));

foreach($artists as $artist) :
	$lastUpdate = Lyrics2Albums::lastUpdate($artist->url, $artist);
	Sitemap::ageData($doc, Url::to(['lyrics/index', 'artist' => $artist->url], true), $lastUpdate, 0.6);
	foreach($artist->albums as $album) :
		$lastUpdate = Lyrics3Tracks::lastUpdate($album->artist->url, $album->year, $album->url, $album);
		Sitemap::ageData($doc, Url::to(['lyrics/index', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url], true), $lastUpdate, 0.5);
	endforeach;
endforeach;

$doc->endElement();
$doc->endDocument();
echo $doc->outputMemory();
