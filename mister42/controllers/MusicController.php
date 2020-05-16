<?php

namespace mister42\controllers;

use mister42\models\music\Collection;
use mister42\models\music\Lyrics1Artists;
use mister42\models\music\Lyrics2Albums;
use mister42\models\music\Lyrics3Tracks;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\web\NotFoundHttpException;

class MusicController extends \yii\web\Controller
{
    private ?string $album;
    private ?string $artist;
    private ?array $data;
    private int $lastModified;
    private ?int $size;
    private ?int $year;

    public function actionCollection(): string
    {
        return $this->render('collection');
    }

    public function actionLyrics(): string
    {
        return $this->render($this->getViewFile(), [
            'data' => $this->data,
        ]);
    }

    public function behaviors(): array
    {
        if ($this->action->id === 'collection') {
            $this->lastModified = Collection::getLastModified();
        } elseif ($this->artist && $this->year && $this->album) {
            $this->data = $this->getAlbum();
            $this->lastModified = Lyrics3Tracks::getLastModified($this->artist, $this->year, $this->album);
        } elseif ($this->artist) {
            $this->data = $this->getArtist();
            $this->lastModified = Lyrics2Albums::getLastModified($this->artist);
        } else {
            $this->data = Lyrics1Artists::artistsList();
            $this->lastModified = Lyrics1Artists::getLastModified();
        }

        return [
            [
                'class' => \yii\filters\HttpCache::class,
                'enabled' => !YII_DEBUG,
                'etagSeed' => function () {
                    return serialize([phpversion(), Yii::$app->user->id, $this->lastModified]);
                },
                'lastModified' => function () {
                    return $this->lastModified;
                },
                'only' => ['index', 'collection'],
            ],
        ];
    }

    public function init(): void
    {
        parent::init();
        foreach (['artist', 'year', 'album', 'size'] as $val) {
            $this->$val = Yii::$app->request->get($val);
        }
    }

    private function getAlbum(): array
    {
        $tracks = Lyrics3Tracks::tracksList($this->artist, $this->year, $this->album);

        if (!ArrayHelper::keyExists(0, $tracks)) {
            throw new NotFoundHttpException('Album not found.');
        }
        if ($tracks[0]->artist->url !== $this->artist || $tracks[0]->album->url !== $this->album) {
            $this->redirect(["/{$this->module->requestedRoute}", 'artist' => $tracks[0]->artist->url, 'year' => $tracks[0]->album->year, 'album' => $tracks[0]->album->url, 'size' => $this->size], 301)->send();
        }

        return $tracks;
    }

    private function getArtist(): array
    {
        $albums = Lyrics2Albums::albumsList($this->artist);

        if (count($albums) === 0) {
            throw new NotFoundHttpException('Artist not found.');
        }
        if ($albums[0]->artist->url !== $this->artist) {
            $this->redirect(["/{$this->module->requestedRoute}", 'artist' => $albums[0]->artist->url], 301)->send();
        }

        return $albums;
    }

    private function getViewFile(): string
    {
        $class = get_class($this->data[0]);
        $class = explode('\\', $class);
        return Inflector::slug(end($class));
    }
}
