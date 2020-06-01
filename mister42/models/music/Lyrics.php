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
        if (!ArrayHelper::isIn($size, [125, 500, 800])) {
            throw new NotFoundHttpException('Cover not found.');
        }

        $data = $this->getTracks($artist, $year, $album, $size);
        return Lyrics2Albums::getCover($size, $data);
    }
    
    public function getAlbumpdf(string $artist, int $year, string $album): array
    {
        $albums = $this->getTracks($artist, $year, $album);
        $data = reset($albums);
        $pdf = Lyrics2Albums::buildPdf($data->album);
        return [$pdf, $data];
    }

    public function getAlbums(string $artist): array
    {
        $albums = Lyrics2Albums::albumsList($artist);

        if (count($albums) === 0) {
            throw new NotFoundHttpException('Artist not found.');
        }
        $album = reset($albums);
        if ($album->artist->url !== $artist) {
            $this->controller->redirect(["/{$this->controller->module->requestedRoute}", 'artist' => $album->artist->url], 301)->send();
        }

        return $albums;
    }

    public function getArtists(): array
    {
        return Lyrics1Artists::artistsList();
    }

    public function getLastModified(InlineAction $action, Request $request): int
    {
#        var_dump($action->id);exit;
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
    
    public function getTracks(string $artist, int $year, string $album, int $size = null): array
    {
        $tracks = Lyrics3Tracks::tracksList($artist, $year, $album);

        if (!ArrayHelper::keyExists(0, $tracks)) {
            throw new NotFoundHttpException('Album not found.');
        }
        $alb = reset($tracks);
        if ($alb->artist->url !== $artist || $alb->album->url !== $album) {
            $this->controller->redirect(["/{$this->controller->module->requestedRoute}", 'artist' => $alb->artist->url, 'year' => $alb->album->year, 'album' => $alb->album->url, 'size' => $size], 301)->send();
        }

        return $tracks;
    }
}
