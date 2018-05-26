<?php
use app\models\Menu;
use app\models\articles\{Articles, Tags};
use app\models\feed\Sitemap;
use app\models\lyrics\{Lyrics1Artists, Lyrics2Albums, Lyrics3Tracks};
use yii\base\View;
use yii\helpers\{ArrayHelper, Url};

$doc = new XMLWriter();
$doc->openMemory();
$doc->setIndent(YII_DEBUG && php_sapi_name() !== 'cli' && (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin));

$doc->startDocument('1.0', 'UTF-8');
$doc->startElement('urlset');
$doc->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
$doc->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
$doc->writeAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');

$languages = array_replace(array_keys(Yii::$app->params['languages']), ['']);
foreach ($languages as $lng) :
	Sitemap::lineItem($doc, Url::to(['site/index', 'language' => $lng], true), filemtime(View::findViewFile('@app/views/site/index')), 1);
endforeach;

foreach (Menu::getUrlList() as $page) :
	foreach ($languages as $lng) :
		Sitemap::lineItem($doc, Url::to([$page, 'language' => $lng], true), filemtime(View::findViewFile('@app/views'.$page)));
	endforeach;
endforeach;

$articles = Articles::find()->orderBy('created')->with('comments')->all();
Sitemap::lineItem($doc, Url::to(['articles/index'], true), end($articles)->updated, 0.8);

foreach ($articles as $article) :
	$lastUpdate = $article['updated'];
	foreach ($article['comments'] as $comment) :
			$lastUpdate = max($lastUpdate, $comment['created']);
	endforeach;
	Sitemap::lineItem($doc, Url::to(['permalink/articles', 'id' => $article->id]), $lastUpdate);
	if ($article['pdf']) :
			Sitemap::lineItem($doc, Url::to(['articles/pdf', 'id' => $article->id, 'title' => $article->url], true), $lastUpdate);
	endif;
	endforeach;
unset($articles);

$tags = Tags::findTagWeights();
$weight = ArrayHelper::getColumn($tags, 'weight');
foreach ($tags as $tag => $value) :
	$lastUpdate = Tags::lastUpdate($tag);
	foreach ($languages as $lng) :
		Sitemap::lineItem($doc, Url::to(['articles/index', 'action' => 'tag', 'q' => $tag, 'language' => $lng], true), $lastUpdate, $value['weight'] / max($weight) - 0.2);
	endforeach;
endforeach;
unset($tags);

foreach ($languages as $lng) :
	Sitemap::lineItem($doc, Url::to(['lyrics/index', 'language' => $lng], true), Lyrics1Artists::lastUpdate());
endforeach;

foreach (Lyrics1Artists::albumsList() as $artist) :
	$lastUpdate = Lyrics2Albums::lastUpdate($artist->url, $artist->albums);
	foreach ($languages as $lng) :
		Sitemap::lineItem($doc, Url::to(['lyrics/index', 'artist' => $artist->url, 'language' => $lng], true), $lastUpdate, 0.65);
	endforeach;
	foreach ($artist->albums as $album) :
		$lastUpdate = Lyrics3Tracks::lastUpdate($album->artist->url, $album->year, $album->url, (object) ['item' => (object) ['album' => $album]]);
		foreach ($languages as $lng) :
			Sitemap::lineItem($doc, Url::to(['lyrics/index', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url, 'language' => $lng], true), $lastUpdate, 0.5);
		endforeach;
		Sitemap::lineItem($doc, Url::to(['lyrics/albumpdf', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url], true), $lastUpdate, 0.5);
	endforeach;
endforeach;

$doc->endElement();
$doc->endDocument();
echo $doc->outputMemory();
