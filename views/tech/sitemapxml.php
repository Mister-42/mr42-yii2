<?php
use app\models\lyrics\Lyrics1Artists;
use app\models\lyrics\Lyrics2Albums;
use app\models\lyrics\Lyrics3Tracks;
use app\models\post\Tags;
use app\models\tech\Sitemap;
use yii\base\View;
use yii\helpers\Url;

$doc = new DOMDocument('1.0', 'UTF-8');
$doc->formatOutput = YII_ENV_DEV;

$urlset = $doc->createElement('urlset');
$urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
$urlset->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
$urlset->setAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
$doc->appendChild($urlset);

Sitemap::prioData($doc, $urlset, Url::home(true), 1, end($posts)->updated);

foreach($pages as $page)
	Sitemap::ageData($doc, $urlset, Url::to([$page], true), filemtime(View::findViewFile('@app/views/' . $page)));

Sitemap::ageData($doc, $urlset, Url::to(['post/index'], true), end($posts)->updated, 0.8);

foreach($posts as $post) {
	$lastUpdate = $post['updated'];
	foreach ($post['comments'] as $comment)
		$lastUpdate = max($lastUpdate, $comment['created']);
	Sitemap::ageData($doc, $urlset, Url::to(['post/index', 'id' => $post['id'], 'title' => $post['title']], true), $lastUpdate);
}

foreach($tags as $tag => $value) {
	$lastUpdate = Tags::lastUpdate($tag);
	Sitemap::prioData($doc, $urlset, Url::to(['post/index', 'action' => 'tag', 'tag' => $tag], true), $tags[$tag] / max($tags) - 0.2, $lastUpdate);
}

Sitemap::ageData($doc, $urlset, Url::to(['lyrics/index'], true), Lyrics1Artists::lastUpdate());

foreach($artists as $artist) {
	$lastUpdate = Lyrics2Albums::lastUpdate($artist['artistUrl']);
	Sitemap::ageData($doc, $urlset, Url::to(['lyrics/index', 'artist' => $artist['artistUrl']], true), $lastUpdate, 0.6);
}

foreach($albums as $album) {
	$lastUpdate = Lyrics3Tracks::lastUpdate($album['artistUrl'], $album['albumYear'], $album['albumUrl']);
	Sitemap::ageData($doc, $urlset, Url::to(['lyrics/index', 'artist' => $album['artistUrl'], 'year' => $album['albumYear'], 'album' => $album['albumUrl']], true), $lastUpdate, 0.5);
}

echo $doc->saveXML();
