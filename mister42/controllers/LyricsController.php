<?php
namespace app\controllers;
use Yii;
use app\models\lyrics\{Lyrics1Artists, Lyrics2Albums, Lyrics3Tracks};
use yii\helpers\{ArrayHelper, Url};
use yii\web\NotFoundHttpException;

class LyricsController extends \yii\web\Controller {
	public function behaviors() {
		return [
			[
				'class' => \yii\filters\HttpCache::className(),
				'etagSeed' => function () {
					$get = Yii::$app->request->get();
					if (!isset($get['artist']) && !isset($get['year']) && !isset($get['album']))
						return serialize([YII_DEBUG, phpversion(), Yii::$app->user->id, Lyrics1Artists::lastUpdate(null)]);
					elseif (isset($get['artist']) && !isset($get['year']) && !isset($get['album']))
						return serialize([YII_DEBUG, phpversion(), Yii::$app->user->id, Lyrics2Albums::albumsList($get['artist'])]);
					elseif (isset($get['artist']) && isset($get['year']) && isset($get['album']))
						return serialize([YII_DEBUG, phpversion(), Yii::$app->user->id, $get['artist'], $get['year'], $get['album']]);
				},
				'lastModified' => function () {
					$get = Yii::$app->request->get();
					if (!isset($get['artist']) && !isset($get['year']) && !isset($get['album']))
						return Lyrics1Artists::lastUpdate(null);
					elseif (isset($get['artist']) && !isset($get['year']) && !isset($get['album']))
						return Lyrics2Albums::lastUpdate($get['artist']);
					elseif (isset($get['artist']) && isset($get['year']) && isset($get['album']))
						return Lyrics3Tracks::lastUpdate($get['artist'], $get['year'], $get['album']);
				},
				'only' => ['index', 'albumpdf', 'albumcover'],
			],
		];
	}

	public function actionIndex() {
		Yii::$app->view->registerMetaTag(['name' => 'google', 'content' => 'notranslate']);
		$get = Yii::$app->request->get();

		if (!isset($get['artist']) && !isset($get['year']) && !isset($get['album'])) {
			return $this->render('1_artists', [
				'artists' => Lyrics1Artists::artistsList(),
			]);
		} elseif (isset($get['artist']) && !isset($get['year']) && !isset($get['album'])) {
			$albums = Lyrics2Albums::albumsList($get['artist']);

			if (count($albums) === 0)
				throw new NotFoundHttpException('Artist not found.');

			if ($albums[0]->artist->url != $get['artist'])
				$this->redirect(['index', 'artist' => $albums[0]->artist->url], 301)->send();

			return $this->render('2_albums', [
				'albums' => $albums,
			]);
		} elseif (isset($get['artist']) && isset($get['year']) && isset($get['album'])) {
			$tracks = Lyrics3Tracks::tracksList($get['artist'], $get['year'], $get['album']);

			if (!ArrayHelper::keyExists(0, $tracks) || (!Yii::$app->user->identity->isAdmin && !$tracks[0]->album->active))
				throw new NotFoundHttpException('Album not found.');

			if ($tracks[0]->artist->url != $get['artist'] || $tracks[0]->album->url != $get['album'])
				$this->redirect(['index', 'artist' => $tracks[0]->artist->url, 'year' => $tracks[0]->album->year, 'album' => $tracks[0]->album->url], 301)->send();

			Yii::$app->view->registerLinkTag(['rel' => 'alternate', 'href' => Url::to(['albumpdf', 'artist' => $tracks[0]->artist->url, 'year' => $tracks[0]->album->year, 'album' => $tracks[0]->album->url], true), 'type' => 'application/pdf', 'title' => 'PDF']);
			if ($tracks[0]->album->image)
				Yii::$app->view->registerMetaTag(['property' => 'og:image', 'content' => Url::to(['cover', 'artist' => $tracks[0]->artist->url, 'year' => $tracks[0]->album->year, 'album' => $tracks[0]->album->url, 'size' => 'cover'], true)]);
			Yii::$app->view->registerMetaTag(['property' => 'og:type', 'content' => 'music.album']);
			return $this->render('3_tracks', [
				'tracks' => $tracks,
			]);
		}
	}

	public function actionAlbumpdf() {
		$get = Yii::$app->request->get();
		$tracks = Lyrics3Tracks::tracksList($get['artist'], $get['year'], $get['album']);

		if (!ArrayHelper::keyExists(0, $tracks) || !$tracks[0]->album->active)
			throw new NotFoundHttpException('Album not found.');

		if ($tracks[0]->artist->url != $get['artist'] || $tracks[0]->album->url != $get['album'])
			$this->redirect(['albumpdf', 'artist' => $tracks[0]->artist->url, 'year' => $tracks[0]->album->year, 'album' => $tracks[0]->album->url], 301)->send();

		$fileName = Lyrics2Albums::buildPdf($tracks[0]->album, $this->renderPartial('albumPdf', ['tracks' => $tracks]));
		Yii::$app->response->sendFile($fileName, implode(' - ', [$tracks[0]->artist->url, $tracks[0]->album->year, $tracks[0]->album->url]).'.pdf');
	}

	public function actionAlbumcover() {
		$get = Yii::$app->request->get();
		$tracks = Lyrics3Tracks::tracksList($get['artist'], $get['year'], $get['album']);

		if (!ArrayHelper::keyExists(0, $tracks) || !ArrayHelper::isIn($get['size'], [100, 500, 800, 'cover']))
			throw new NotFoundHttpException('Cover not found.');

		if ($tracks[0]->artist->url != $get['artist'] || $tracks[0]->album->url != $get['album'])
			$this->redirect(['albumcover', 'artist' => $tracks[0]->artist->url, 'year' => $tracks[0]->album->year, 'album' => $tracks[0]->album->url], 301)->send();

		list($fileName, $image) = Lyrics2Albums::getCover($get['size'], $tracks);
		return Yii::$app->response->sendContentAsFile($image, $fileName, ['mimeType' => 'image/jpeg', 'inline' => true]);
	}
}
