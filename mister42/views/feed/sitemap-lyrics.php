<?php

use app\models\feed\Sitemap;
use app\models\music\{Lyrics1Artists, Lyrics2Albums, Lyrics3Tracks};

$doc = Sitemap::beginDoc();

Sitemap::lineItem($doc, ['music/lyrics'], ['age' => Lyrics1Artists::getLastModified(), 'locale' => true]);

foreach (Lyrics1Artists::albumsList() as $artist) {
	$lastModified = Lyrics2Albums::getLastModified($artist->url);
	Sitemap::lineItem($doc, ['music/lyrics', 'artist' => $artist->url], ['age' => $lastModified, 0.65, 'locale' => true]);
	foreach ($artist->albums as $album) {
		$lastModified = Lyrics3Tracks::getLastModified($album->artist->url, $album->year, $album->url);
		Sitemap::lineItem($doc, ['music/lyrics', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url], ['age' => $lastModified, 'priority' => 0.5, 'locale' => true]);
		Sitemap::lineItem($doc, ['music/albumpdf', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url], ['age' => $lastModified, 'priority' => 0.5]);
	}
}

echo Sitemap::endDoc($doc);
