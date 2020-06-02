<?php

namespace mister42\models\music;

use yii\base\InlineAction;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Request;

class Lyrics
{
    private $controller;

    public function __construct(object $controller)
    {
        $this->controller = $controller;
    }
    
    public function getAlbumcover(string $artist, int $year, string $album, int $size): array
    {
        $data = $this->getAlbumTracks($artist, $year, $album, $size);
        return Lyrics2Albums::getCover($size, $data);
    }
    
    public function getAlbumPdf(string $artist, int $year, string $album): string
    {
        $data = $this->getAlbumTracks($artist, $year, $album);
        return Lyrics2Albums::buildPdf($data);
    }

    public function getAlbums(string $artist): array
    {
        $albums = Lyrics2Albums::ArtisAlbums($artist);

        if (count($albums) === 0) {
            throw new NotFoundHttpException('Artist not found.');
        }
        $album = reset($albums);
        if ($album->artist->url !== $artist) {
            $this->controller->redirect(["/{$this->controller->module->requestedRoute}", 'artist' => $album->artist->url], 301)->send();
        }

        return $albums;
    }

    public function getAlbumTracks(string $artist, int $year, string $album, int $size = null): Lyrics2Albums
    {
        if (is_int($size) && !ArrayHelper::isIn($size, [125, 500, 800])) {
            throw new NotFoundHttpException('Cover not found.');
        }

        $data = Lyrics2Albums::album($artist, $year, $album);
        if (is_null($data)) {
            throw new NotFoundHttpException('Album not found.');
        }

        if ($data->artist->url !== $artist || $data->url !== $album) {
            $this->controller->redirect(["/{$this->controller->module->requestedRoute}", 'artist' => $data->artist->url, 'year' => $data->year, 'album' => $data->url, 'size' => $size], 301)->send();
        }

        return $data;
    }

    public function getArtists(): array
    {
        return Lyrics1Artists::artistsList();
    }

    public function getLastModified(InlineAction $action, Request $request): int
    {
        switch ($action->id) {
            case 'lyrics1artists':
                return Lyrics1Artists::getLastModified();
            case 'lyrics2albums':
                return Lyrics2Albums::getLastModified($request->get('artist'));
            case 'albumcover':
            case 'albumpdf':
            case 'lyrics3tracks':
                return Lyrics3Tracks::getLastModified($request->get('artist'), $request->get('year'), $request->get('album'));
            case 'collection':
                return Collection::getLastModified();
            case 'collection-cover':
                return Collection::getEntryLastModified($request->get('id'));
            default:
                return time();
        }
    }
}
