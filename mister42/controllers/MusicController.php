<?php

namespace mister42\controllers;

class MusicController extends \mister42\controllers\music\BaseController
{
    public function actionCollection(): string
    {
        return $this->render('collection');
    }

    public function actionLyrics1artists(): string
    {
        return $this->render('lyrics1artists', [
            'data' => $this->getArtists(),
        ]);
    }

    public function actionLyrics2albums(string $artist): string
    {
        return $this->render('lyrics2albums', [
            'data' => $this->getAlbums($artist),
        ]);
    }

    public function actionLyrics3tracks(string $artist, int $year, string $album): string
    {
        return $this->render('lyrics3tracks', [
            'album' => $this->getAlbumTracks($artist, $year, $album),
        ]);
    }

    public function actionTest()
    {
        $model = new \mister42\models\test\Test();
        $data = $model->getList();
        var_dump($data);
        exit;
    }
}
