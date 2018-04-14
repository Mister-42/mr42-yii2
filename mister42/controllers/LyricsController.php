<?php
namespace app\controllers;
use Yii;
use app\models\lyrics\{Lyrics1Artists, Lyrics2Albums, Lyrics3Tracks};
use yii\helpers\{ArrayHelper, Url};
use yii\web\NotFoundHttpException;

class LyricsController extends \yii\web\Controller {
	const PAGE_INDEX = '0';
	const PAGE_ARTIST = '1';
	const PAGE_ALBUM = '2';

	public $page;
	public $artist;
	public $year;
	public $album;
	public $size;

	public function init() {
		$this->artist = Yii::$app->request->get('artist');
		$this->year = Yii::$app->request->get('year');
		$this->album = Yii::$app->request->get('album');
		$this->size = Yii::$app->request->get('size');

		if (!isset($this->artist) && !isset($this->year) && !isset($this->album))
			$this->page = Self::PAGE_INDEX;
		elseif (isset($this->artist) && !isset($this->year) && !isset($this->album))
			$this->page = Self::PAGE_ARTIST;
		elseif (isset($this->artist) && isset($this->year) && isset($this->album))
			$this->page = Self::PAGE_ALBUM;

		parent::init();
	}

	public function behaviors() {
		return [
			[
				'class' => \yii\filters\HttpCache::class,
				'etagSeed' => function () {
					switch ($this->page) {
						case Self::PAGE_INDEX:
							return serialize([YII_DEBUG, phpversion(), Yii::$app->user->id, Lyrics1Artists::lastUpdate(null)]);
							break;
						case Self::PAGE_ARTIST:
							return serialize([YII_DEBUG, phpversion(), Yii::$app->user->id, Lyrics2Albums::lastUpdate($this->artist)]);
							break;
						case Self::PAGE_ALBUM:
							return serialize([YII_DEBUG, phpversion(), Yii::$app->user->id, Lyrics3Tracks::lastUpdate($this->artist, $this->year, $this->album)]);
							break;
					}
				},
				'lastModified' => function () {
					switch ($this->page) {
						case Self::PAGE_INDEX:	return Lyrics1Artists::lastUpdate(null); break;
						case Self::PAGE_ARTIST:	return Lyrics2Albums::lastUpdate($this->artist); break;
						case Self::PAGE_ALBUM:	return Lyrics3Tracks::lastUpdate($this->artist, $this->year, $this->album); break;
					}
				},
				'only' => ['index', 'albumpdf', 'albumcover'],
			],
		];
	}

	public function actionIndex() {
		switch ($this->page) {
			case Self::PAGE_INDEX:	list($page, $data) = ['1_index', Lyrics1Artists::artistsList()];				break;
			case Self::PAGE_ARTIST:	list($page, $data) = Self::pageArtist($this->artist);							break;
			case Self::PAGE_ALBUM:	list($page, $data) = Self::pageAlbum($this->artist, $this->year, $this->album);	break;
		}

		Yii::$app->view->registerMetaTag(['name' => 'google', 'content' => 'notranslate']);
		return $this->render($page, [
			'data' => $data,
		]);
	}

	public function actionAlbumpdf() {
		$tracks = Lyrics3Tracks::tracksList($this->artist, $this->year, $this->album);

		if (!ArrayHelper::keyExists(0, $tracks) || !$tracks[0]->album->active)
			throw new NotFoundHttpException('Album not found.');

		if ($tracks[0]->artist->url != $this->artist || $tracks[0]->album->url != $this->album)
			$this->redirect(['albumpdf', 'artist' => $tracks[0]->artist->url, 'year' => $tracks[0]->album->year, 'album' => $tracks[0]->album->url], 301)->send();

		$fileName = Lyrics2Albums::buildPdf($tracks[0]->album, $this->renderPartial('albumPdf', ['tracks' => $tracks]));
		Yii::$app->response->sendFile($fileName, implode(' - ', [$tracks[0]->artist->url, $tracks[0]->album->year, $tracks[0]->album->url]).'.pdf');
	}

	public function actionAlbumcover() {
		$tracks = Lyrics3Tracks::tracksList($this->artist, $this->year, $this->album);

		if (!ArrayHelper::keyExists(0, $tracks) || !ArrayHelper::isIn($this->size, [100, 500, 800, 'cover']))
			throw new NotFoundHttpException('Cover not found.');

		if ($tracks[0]->artist->url != $this->artist || $tracks[0]->album->url != $this->album)
			$this->redirect(['albumcover', 'artist' => $tracks[0]->artist->url, 'year' => $tracks[0]->album->year, 'album' => $tracks[0]->album->url], 301)->send();

		list($fileName, $image) = Lyrics2Albums::getCover($this->size, $tracks);
		return Yii::$app->response->sendContentAsFile($image, $fileName, ['mimeType' => 'image/jpeg', 'inline' => true]);
	}

	private function pageArtist ($artist) {
		$albums = Lyrics2Albums::albumsList($artist);

		if (count($albums) === 0)
			throw new NotFoundHttpException('Artist not found.');

		if ($albums[0]->artist->url != $artist)
			$this->redirect(['index', 'artist' => $albums[0]->artist->url], 301)->send();

		return ['2_artist', $albums];
	}

	private function pageAlbum ($artist, $year, $album) {
		$tracks = Lyrics3Tracks::tracksList($artist, $year, $album);

		if (!ArrayHelper::keyExists(0, $tracks) || (!Yii::$app->user->identity->isAdmin && !$tracks[0]->album->active))
			throw new NotFoundHttpException('Album not found.');

		if ($tracks[0]->artist->url != $this->artist || $tracks[0]->album->url != $this->album)
			$this->redirect(['index', 'artist' => $tracks[0]->artist->url, 'year' => $tracks[0]->album->year, 'album' => $tracks[0]->album->url], 301)->send();

		Yii::$app->view->registerLinkTag(['rel' => 'alternate', 'href' => Url::to(['albumpdf', 'artist' => $tracks[0]->artist->url, 'year' => $tracks[0]->album->year, 'album' => $tracks[0]->album->url], true), 'type' => 'application/pdf', 'title' => 'PDF']);
		if ($tracks[0]->album->image)
			Yii::$app->view->registerMetaTag(['property' => 'og:image', 'content' => Url::to(['cover', 'artist' => $tracks[0]->artist->url, 'year' => $tracks[0]->album->year, 'album' => $tracks[0]->album->url, 'size' => 'cover'], true)]);
		Yii::$app->view->registerMetaTag(['property' => 'og:type', 'content' => 'music.album']);

		return ['3_album', $tracks];
	}
}
