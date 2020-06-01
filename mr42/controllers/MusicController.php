<?php

namespace mr42\controllers;

use mister42\models\music\Collection;
use mister42\models\music\Lyrics;
use Yii;
use yii\web\Response;

class MusicController extends \yii\web\Controller
{
    private $lyrics;

    public function actionAlbumcover(string $artist, int $year, string $album, int $size): Response
    {
        [$fileName, $image] = $this->lyrics->getAlbumcover($artist, $year, $album, $size);
        return Yii::$app->response->sendContentAsFile($image, $fileName, ['mimeType' => 'image/jpeg', 'inline' => true]);
    }

    public function actionAlbumpdf(string $artist, int $year, string $album): Response
    {
        [$fileName, $data] = $this->lyrics->getAlbumpdf($artist, $year, $album);
        return Yii::$app->response->sendFile($fileName, implode(' - ', [$data->artist->url, $data->album->year, $data->album->url]) . '.pdf', ['inline' => true]);
    }

    public function actionCollectionCover(int $id): Response
    {
        $album = Collection::find()->where(['id' => $id])->one();
        if (!$album || !$album->image) {
            return Yii::$app->response->sendFile(Yii::getAlias('@assetsroot/images/nocdcover.png'), null, ['inline' => true]);
        }
        return Yii::$app->response->sendContentAsFile($album->image, "{$id}.jpg", ['mimeType' => 'image/jpeg', 'inline' => true]);
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => \yii\filters\HttpCache::class,
                'enabled' => !YII_DEBUG,
                'etagSeed' => function ($action) {
                    return serialize([phpversion(), $this->lyrics->getLastModified($action, Yii::$app->request)]);
                },
                'lastModified' => function ($action) {
                    return $this->lyrics->getLastModified($action, Yii::$app->request);
                },
            ],
        ];
    }

    public function init(): void
    {
        parent::init();
        $this->lyrics = new Lyrics($this);
    }
}
