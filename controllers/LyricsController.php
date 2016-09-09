<?php
namespace app\controllers;
use Yii;
use app\models\Pdf;
use app\models\lyrics\Lyrics1Artists;
use app\models\lyrics\Lyrics2Albums;
use app\models\lyrics\Lyrics3Tracks;
use app\models\user\RecentTracks;
use yii\filters\HttpCache;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;

class LyricsController extends Controller
{
	public function behaviors()
	{
		return [
			[
				'class' => HttpCache::className(),
				'etagSeed' => function ($action, $params) {
					$urlData	= Yii::$app->request->get();
					if (!isset($urlData['artist']) && !isset($urlData['year']) && !isset($urlData['album'])) {
						return serialize([YII_DEBUG, Yii::$app->user->id, Lyrics1Artists::lastUpdate()]);
					} elseif (isset($urlData['artist']) && !isset($urlData['year']) && !isset($urlData['album'])) {
						return serialize([YII_DEBUG, Yii::$app->user->id, Lyrics2Albums::albumsList($urlData['artist'])]);
					} elseif (isset($urlData['artist']) && isset($urlData['year']) && isset($urlData['album'])) {
						return serialize([YII_DEBUG, Yii::$app->user->id, $urlData['artist'], $urlData['year'], $urlData['album']]);
					}
				},
				'lastModified' => function ($action, $params) {
					$urlData	= Yii::$app->request->get();
					if (!isset($urlData['artist']) && !isset($urlData['year']) && !isset($urlData['album'])) {
						return Lyrics1Artists::lastUpdate();
					} elseif (isset($urlData['artist']) && !isset($urlData['year']) && !isset($urlData['album'])) {
						return Lyrics2Albums::lastUpdate($urlData['artist']);
					} elseif (isset($urlData['artist']) && isset($urlData['year']) && isset($urlData['album'])) {
						return Lyrics3Tracks::lastUpdate($urlData['artist'], $urlData['year'], $urlData['album']);
					}
				},
				'only' => ['index'],
			],
		];
	}

	public function actionIndex()
	{
		Yii::$app->view->registerMetaTag(['name' => 'google', 'content' => 'notranslate']);
		$urlData	= Yii::$app->request->get();

		if (!isset($urlData['artist']) && !isset($urlData['year']) && !isset($urlData['album'])) {
			$this->layout = '@app/views/layouts/recenttracks.php';
			return $this->render('1_artists', [
				'artists' => Lyrics1Artists::artistsList(),
			]);
		} elseif (isset($urlData['artist']) && !isset($urlData['year']) && !isset($urlData['album'])) {
			$albums = Lyrics2Albums::albumsList($urlData['artist']);

			if (count($albums) === 0)
				throw new NotFoundHttpException('Artist not found.');

			return $this->render('2_albums', [
					'albums' => $albums,
			]);		
		} elseif (isset($urlData['artist']) && isset($urlData['year']) && isset($urlData['album'])) {
			$tracks = Lyrics3Tracks::tracksList($urlData['artist'], $urlData['year'], $urlData['album'], 'full');

			if (count($tracks) === 0)
				throw new NotFoundHttpException('Album not found.');

			Yii::$app->view->registerLinkTag(['rel' => 'alternate', 'href' => Url::to(['albumpdf', 'artist' => $tracks[0]['artistUrl'], 'year' => $tracks[0]['albumYear'], 'album' => $tracks[0]['albumUrl']], true), 'type' => 'application/pdf', 'title' => 'PDF']);
			return $this->render('3_tracks', [
				'tracks' => $tracks,
			]);
		}
	}

	public function actionRecenttracks()
	{
		if (!Yii::$app->request->isAjax)
			throw new MethodNotAllowedHttpException('Method Not Allowed.');

		RecentTracks::display(1);
	}

	public function actionAlbumpdf()
	{
		$urlData	= Yii::$app->request->get();
		$tracks = Lyrics3Tracks::tracksList($urlData['artist'], $urlData['year'], $urlData['album'], 'full');

		if (count($tracks) === 0)
			throw new NotFoundHttpException('Album not found.');

		$pdf = new Pdf();
		$fileName = $pdf->create(
			'@runtime/pdf/lyrics/'.implode(' - ', [$tracks[0]['artistUrl'], $tracks[0]['albumYear'], $tracks[0]['albumUrl']]),
			$this->renderPartial('albumPdf', ['tracks' => $tracks]),
			Lyrics3Tracks::lastUpdate($tracks[0]['artistUrl'], $tracks[0]['albumYear'], $tracks[0]['albumUrl']),
			[
				'author' => $tracks[0]['artistName'],
				'footer' => Html::a(Yii::$app->name, Url::to(Yii::$app->homeUrl, true)).'|'.$tracks[0]['albumYear'].'|Page {PAGENO} of {nb}',
				'header' => $tracks[0]['artistName'].'||'.$tracks[0]['albumName'],
				'keywords' => implode(', ', [$tracks[0]['artistName'], $tracks[0]['albumName'], 'lyrics']),
				'subject' => $tracks[0]['artistName'].' - '.$tracks[0]['albumName'],
				'title' => implode(' - ', [$tracks[0]['artistName'], $tracks[0]['albumName']]),
			]		
		);
		Yii::$app->response->sendFile($fileName, implode(' - ', [$tracks[0]['artistUrl'], $tracks[0]['albumYear'], $tracks[0]['albumUrl']]).'.pdf');
	}
}
