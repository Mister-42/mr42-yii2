<?php

namespace mr42\controllers;

use mister42\models\music\Collection;
use Yii;
use yii\web\Response;

class MusicController extends \mister42\controllers\music\LyricsController
{
    public function actionAlbumcover(string $artist, int $year, string $album, int $size): Response
    {
        [$fileName, $image] = $this->getAlbumcover($artist, $year, $album, $size);
        return Yii::$app->response->sendContentAsFile($image, $fileName, ['mimeType' => 'image/jpeg', 'inline' => true]);
    }

    public function actionAlbumpdf(string $artist, int $year, string $album): Response
    {
        $fileName = $this->getAlbumPdf($artist, $year, $album);
        return Yii::$app->response->sendFile($fileName, implode(' - ', [$artist, $year, $album]) . '.pdf', ['inline' => true]);
    }

    public function actionCollectionCover(int $id): Response
    {
        $album = Collection::find()->where(['id' => $id])->one();
        if (!$album || !$album->image) {
            return Yii::$app->response->sendFile(Yii::getAlias('@assetsroot/images/nocdcover.png'), null, ['inline' => true]);
        }
        return Yii::$app->response->sendContentAsFile($album->image, "{$id}.jpg", ['mimeType' => 'image/jpeg', 'inline' => true]);
    }
}
