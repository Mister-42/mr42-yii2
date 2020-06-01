<?php

namespace mister42\controllers;

use mister42\models\music\Lyrics;
use Yii;

class MusicController extends \yii\web\Controller
{
    private $lyrics;

    public function actionCollection(): string
    {
        return $this->render('collection');
    }

    public function actionLyrics1artists(): string
    {
        return $this->render('lyrics1artists', [
            'data' => $this->lyrics->getArtists(),
        ]);
    }

    public function actionLyrics2albums(string $artist): string
    {
        return $this->render('lyrics2albums', [
            'data' => $this->lyrics->getAlbums($artist),
        ]);
    }

    public function actionLyrics3tracks(string $artist, int $year, string $album): string
    {
        return $this->render('lyrics3tracks', [
            'data' => $this->lyrics->getTracks($artist, $year, $album),
        ]);
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => \yii\filters\HttpCache::class,
                'enabled' => !YII_DEBUG,
                'etagSeed' => function ($action) {
                    return serialize([phpversion(), Yii::$app->user->id, $this->lyrics->getLastModified($action, Yii::$app->request)]);
                },
                'lastModified' => function ($action) {
                    return $this->lyrics->getLastModified($action, Yii::$app->request);
                }
            ],
        ];
    }

    public function init()
    {
        parent::init();
        $this->lyrics = new Lyrics($this);
    }
}
