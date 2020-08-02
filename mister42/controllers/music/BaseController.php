<?php

namespace mister42\controllers\music;

use mister42\models\music\Collection;
use mister42\models\music\Lyrics1Artists;
use mister42\models\music\Lyrics2Albums;
use mister42\models\music\Lyrics3Tracks;
use Yii;
use yii\base\InlineAction;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\web\NotFoundHttpException;

class BaseController extends \yii\web\Controller
{
    public function behaviors(): array
    {
        return [
            [
                'class' => \yii\filters\HttpCache::class,
                'enabled' => !YII_DEBUG,
                'etagSeed' => function ($action) {
                    $namespace = (new \ReflectionObject($this))->getNamespaceName();
                    return serialize([phpversion(), (StringHelper::startsWith($namespace, 'mr42') ? null : Yii::$app->user->id), $this->getLastModified($action)]);
                },
                'lastModified' => function ($action) {
                    return $this->getLastModified($action);
                },
            ],
        ];
    }

    protected function getAlbumcover(string $artist, int $year, string $album, int $size): array
    {
        $data = $this->getAlbumTracks($artist, $year, $album, $size);
        return Lyrics2Albums::getCover($size, $data);
    }

    protected function getAlbumPdf(string $artist, int $year, string $album): string
    {
        $data = $this->getAlbumTracks($artist, $year, $album);
        return Lyrics2Albums::buildPdf($data);
    }

    protected function getAlbums(string $artist): array
    {
        $albums = Lyrics2Albums::getArtistAlbums($artist);
        if (count($albums) === 0) {
            throw new NotFoundHttpException('Artist not found.');
        }

        $album = reset($albums);
        if ($album->artist->url !== $artist) {
            $this->redirect(["/{$this->module->requestedRoute}", 'artist' => $album->artist->url], 301)->send();
        }

        return $albums;
    }

    protected function getAlbumTracks(string $artist, int $year, string $album, int $size = null): Lyrics2Albums
    {
        if (is_int($size) && !ArrayHelper::isIn($size, [125, 500, 800])) {
            throw new NotFoundHttpException('Cover not found.');
        }

        $data = Lyrics2Albums::getAlbum($artist, $year, $album);
        if (is_null($data)) {
            throw new NotFoundHttpException('Album not found.');
        }

        if ($data->artist->url !== $artist || $data->url !== $album) {
            $this->redirect(["/{$this->module->requestedRoute}", 'artist' => $data->artist->url, 'year' => $data->year, 'album' => $data->url, 'size' => $size], 301)->send();
        }

        return $data;
    }

    protected function getArtists(): array
    {
        return Lyrics1Artists::artistsList();
    }

    private function getLastModified(InlineAction $action): int
    {
        $request = Yii::$app->request;
        switch ($action->id) {
            case 'lyrics1artists':
                $lastMod = Lyrics1Artists::getLastModified();
            break;
            case 'lyrics2albums':
                $lastMod = Lyrics2Albums::getLastModified($request->get('artist'));
            break;
            case 'albumcover':
            case 'albumpdf':
            case 'lyrics3tracks':
                $lastMod = Lyrics3Tracks::getLastModified($request->get('artist'), $request->get('year'), $request->get('album'));
            break;
            case 'collection':
                $lastMod = Collection::getLastModified();
            break;
            case 'collection-cover':
                $lastMod = Collection::getEntryLastModified($request->get('id'));
            break;
        }

        return $lastMod ?? time();
    }
}
