<?php

use mister42\models\music\Lyrics1Artists;
use mister42\models\music\Lyrics2Albums;
use mister42\models\music\Lyrics3Tracks;
use mr42\models\Sitemap;

$doc = Sitemap::beginDoc();

Sitemap::lineItem($doc, ['music/lyrics'], ['age' => Lyrics1Artists::getLastModified(), 'locale' => true]);

foreach (Lyrics1Artists::albumsList() as $artist) {
    $lastModified = Lyrics2Albums::getLastModified($artist->url);
    Sitemap::lineItem($doc, ['music/lyrics2albums', 'artist' => $artist->url], ['age' => $lastModified, 0.65, 'locale' => true]);
    foreach ($artist->albums as $album) {
        $lastModified = Lyrics3Tracks::getLastModified($album->artist->url, $album->year, $album->url);
        Sitemap::lineItem($doc, ['music/lyrics3tracks', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url], ['age' => $lastModified, 'priority' => 0.5, 'locale' => true]);
        $pdfUrl = Yii::$app->mr42->createUrl(['music/albumpdf', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url]);
        Sitemap::lineItem($doc, $pdfUrl, ['age' => $lastModified, 'priority' => 0.5]);
    }
}

echo Sitemap::endDoc($doc);
