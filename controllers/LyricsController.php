<?php
namespace app\controllers;
use Yii;
use app\models\lyrics\{Lyrics1Artists, Lyrics2Albums, Lyrics3Tracks};
use yii\filters\HttpCache;
use yii\helpers\Url;
use yii\web\{Controller, MethodNotAllowedHttpException, NotFoundHttpException};

class LyricsController extends Controller {
	public function behaviors() {
		return [
			[
				'class' => HttpCache::className(),
				'etagSeed' => function ($action, $params) {
					$get = Yii::$app->request->get();
					if (!isset($get['artist']) && !isset($get['year']) && !isset($get['album']))
						return serialize([YII_DEBUG, Yii::$app->user->id, Lyrics1Artists::lastUpdate(null)]);
					elseif (isset($get['artist']) && !isset($get['year']) && !isset($get['album']))
						return serialize([YII_DEBUG, Yii::$app->user->id, Lyrics2Albums::albumsList($get['artist'])]);
					elseif (isset($get['artist']) && isset($get['year']) && isset($get['album']))
						return serialize([YII_DEBUG, Yii::$app->user->id, $get['artist'], $get['year'], $get['album']]);
				},
				'lastModified' => function ($action, $params) {
					$get = Yii::$app->request->get();
					if (!isset($get['artist']) && !isset($get['year']) && !isset($get['album']))
						return Lyrics1Artists::lastUpdate(null);
					elseif (isset($get['artist']) && !isset($get['year']) && !isset($get['album']))
						return Lyrics2Albums::lastUpdate($get['artist']);
					elseif (isset($get['artist']) && isset($get['year']) && isset($get['album']))
						return Lyrics3Tracks::lastUpdate($get['artist'], $get['year'], $get['album']);
				},
				'only' => ['index', 'albumpdf'],
			],
		];
	}

	public function actionIndex() {
		Yii::$app->view->registerMetaTag(['name' => 'google', 'content' => 'notranslate']);
		$get = Yii::$app->request->get();

		if (!isset($get['artist']) && !isset($get['year']) && !isset($get['album'])) {
			$this->layout = '@app/views/layouts/recenttracks.php';
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
			$tracks = Lyrics3Tracks::tracksListFull($get['artist'], $get['year'], $get['album']);

			if (count($tracks) === 0)
				throw new NotFoundHttpException('Album not found.');

			if ($tracks[0]->artist->url != $get['artist'] || $tracks[0]->album->url != $get['album'])
				$this->redirect(['index', 'artist' => $tracks[0]->artist->url, 'year' => $tracks[0]->album->year, 'album' => $tracks[0]->album->url], 301)->send();

			Yii::$app->view->registerLinkTag(['rel' => 'alternate', 'href' => Url::to(['albumpdf', 'artist' => $tracks[0]->artist->url, 'year' => $tracks[0]->album->year, 'album' => $tracks[0]->album->url], true), 'type' => 'application/pdf', 'title' => 'PDF']);
			return $this->render('3_tracks', [
				'tracks' => $tracks,
			]);
		}
	}

	public function actionRecenttracks() {
		if (!Yii::$app->request->isAjax)
			throw new MethodNotAllowedHttpException('Method Not Allowed.');

		return $this->renderAjax('recentTracks', [
			'userid' => 1,
		]);
	}

	public function actionAlbumpdf() {
		$get = Yii::$app->request->get();
		$tracks = Lyrics3Tracks::tracksListFull($get['artist'], $get['year'], $get['album']);
		$html = $this->renderPartial('albumPdf', ['tracks' => $tracks]);

		if (count($tracks) === 0)
			throw new NotFoundHttpException('Album not found.');

		if ($tracks[0]->artist->url != $get['artist'] || $tracks[0]->album->url != $get['album'])
			$this->redirect(['albumpdf', 'artist' => $tracks[0]->artist->url, 'year' => $tracks[0]->album->year, 'album' => $tracks[0]->album->url], 301)->send();

		$fileName = Lyrics2Albums::buildPdf($tracks, $html);
		Yii::$app->response->sendFile($fileName, implode(' - ', [$tracks[0]->artist->url, $tracks[0]->album->year, $tracks[0]->album->url]).'.pdf');
	}
}
