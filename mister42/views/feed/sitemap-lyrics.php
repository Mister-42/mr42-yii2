<?php
use app\models\feed\Sitemap;
use app\models\lyrics\{Lyrics1Artists, Lyrics2Albums, Lyrics3Tracks};

$doc = Sitemap::beginDoc();

Sitemap::lineItem($doc, ['lyrics/index'], ['age' => Lyrics1Artists::lastUpdate(), 'locale' => true]);

foreach (Lyrics1Artists::albumsList() as $artist) :
	$lastUpdate = Lyrics2Albums::lastUpdate($artist->url, $artist->albums);
	Sitemap::lineItem($doc, ['lyrics/index', 'artist' => $artist->url], ['age' => $lastUpdate, 0.65, 'locale' => true]);
	foreach ($artist->albums as $album) :
		$lastUpdate = Lyrics3Tracks::lastUpdate($album->artist->url, $album->year, $album->url, (object) ['item' => (object) ['album' => $album]]);
		Sitemap::lineItem($doc, ['lyrics/index', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url], ['age' => $lastUpdate, 'priority' => 0.5, 'locale' => true]);
		Sitemap::lineItem($doc, ['lyrics/albumpdf', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url], ['age' => $lastUpdate, 'priority' => 0.5]);
	endforeach;
endforeach;

echo Sitemap::endDoc($doc);
