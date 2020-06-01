<?php

namespace mister42\controllers;

use mister42\models\music\Collection;
use mister42\models\music\Lyrics1Artists;
use mister42\models\music\Lyrics2Albums;
use mister42\models\music\Lyrics3Tracks;
use Yii;
use yii\base\InlineAction;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Request;

class MusicController extends \yii\web\Controller
{
    public function actionCollection(): string
    {
        return $this->render('collection');
    }

    public function actionLyrics1artists(): string
    {
        return $this->render('lyrics1artists', [
            'data' => Lyrics1Artists::artistsList(),
        ]);
    }

    public function actionLyrics2albums(string $artist): string
    {
        $albums = Lyrics2Albums::albumsList($artist);

        if (count($albums) === 0) {
            throw new NotFoundHttpException('Artist not found.');
        }
        if ($albums[0]->artist->url !== $artist) {
            $this->redirect(["/{$this->module->requestedRoute}", 'artist' => $albums[0]->artist->url], 301)->send();
        }

        return $this->render('lyrics2albums', [
            'data' => $albums,
        ]);
    }

    public function actionLyrics3tracks(string $artist, int $year, string $album): string
    {
        $tracks = Lyrics3Tracks::tracksList($artist, $year, $album);

        if (!ArrayHelper::keyExists(0, $tracks)) {
            throw new NotFoundHttpException('Album not found.');
        }
        if ($tracks[0]->artist->url !== $artist || $tracks[0]->album->url !== $album) {
            $this->redirect(["/{$this->module->requestedRoute}", 'artist' => $tracks[0]->artist->url, 'year' => $tracks[0]->album->year, 'album' => $tracks[0]->album->url], 301)->send();
        }

        return $this->render('lyrics3tracks', [
            'data' => $tracks,
        ]);
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => \yii\filters\HttpCache::class,
                'enabled' => !YII_DEBUG,
                'etagSeed' => function ($action) {
                    return serialize([phpversion(), Yii::$app->user->id, $this->getLastModified($action, Yii::$app->request)]);
                },
                'lastModified' => function ($action) {
                    return $this->getLastModified($action, Yii::$app->request);
                }
            ],
        ];
    }

    private function getLastModified(InlineAction $action, Request $request): int
    {
        switch ($action->id) {
            case 'lyrics1artists':
                return Lyrics1Artists::getLastModified();
            case 'lyrics2albums':
                return Lyrics2Albums::getLastModified($request->get('artist'));
            case 'lyrics3tracks':
                return Lyrics3Tracks::getLastModified($request->get('artist'), $request->get('year'), $request->get('album'));
            default:
                return Collection::getLastModified();
        }
    }
}
