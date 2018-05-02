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
	public $lastModified;

	public function init() {
		$this->artist = Yii::$app->request->get('artist');
		$this->year = Yii::$app->request->get('year');
		$this->album = Yii::$app->request->get('album');
		$this->size = Yii::$app->request->get('size');

		if ($this->artist && $this->year && $this->album) {
			$this->page = self::PAGE_ALBUM;
			$this->lastModified = Lyrics3Tracks::lastUpdate($this->artist, $this->year, $this->album);
		} elseif ($this->artist) {
			$this->page = self::PAGE_ARTIST;
			$this->lastModified = Lyrics2Albums::lastUpdate($this->artist);
		} else {
			$this->page = self::PAGE_INDEX;
			$this->lastModified = Lyrics1Artists::lastUpdate(null);
		}

		parent::init();
	}

	public function behaviors() {
		return [
			[
				'class' => \yii\filters\HttpCache::class,
				'enabled' => !YII_DEBUG,
				'etagSeed' => function () { return serialize([phpversion(), Yii::$app->user->id, $this->lastModified]); },
				'lastModified' => function () { return $this->lastModified; },
				'only' => ['index', 'albumpdf', 'albumcover'],
			],
		];
	}

	public function actionIndex() {
		switch ($this->page) {
			case self::PAGE_INDEX:	list($page, $data) = ['1_index', Lyrics1Artists::artistsList()];	break;
			case self::PAGE_ARTIST:	list($page, $data) = self::pageArtist();							break;
			case self::PAGE_ALBUM:	list($page, $data) = self::pageAlbum();								break;
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
		self::redirectIfNotUrl('albumpdf', $tracks);

		$fileName = Lyrics2Albums::buildPdf($tracks[0]->album, $this->renderPartial('albumPdf', ['tracks' => $tracks]));
		return Yii::$app->response->sendFile($fileName, implode(' - ', [$tracks[0]->artist->url, $tracks[0]->album->year, $tracks[0]->album->url]).'.pdf');
	}

	public function actionAlbumcover() {
		$tracks = Lyrics3Tracks::tracksList($this->artist, $this->year, $this->album);

		if (!ArrayHelper::keyExists(0, $tracks) || !ArrayHelper::isIn($this->size, [100, 500, 800, 'cover']))
			throw new NotFoundHttpException('Cover not found.');
		self::redirectIfNotUrl('albumcover', $tracks);

		list($fileName, $image) = Lyrics2Albums::getCover($this->size, $tracks);
		return Yii::$app->response->sendContentAsFile($image, $fileName, ['mimeType' => 'image/jpeg', 'inline' => true]);
	}

	private function pageArtist (): array {
		$albums = Lyrics2Albums::albumsList($this->artist);

		if (count($albums) === 0)
			throw new NotFoundHttpException('Artist not found.');

		if ($albums[0]->artist->url !== $this->artist)
			$this->redirect(['index', 'artist' => $albums[0]->artist->url], 301)->send();

		return ['2_artist', $albums];
	}

	private function pageAlbum (): array {
		$tracks = Lyrics3Tracks::tracksList($this->artist, $this->year, $this->album);

		if (!ArrayHelper::keyExists(0, $tracks) || (!Yii::$app->user->identity->isAdmin && !$tracks[0]->album->active))
			throw new NotFoundHttpException('Album not found.');
		self::redirectIfNotUrl('index', $tracks);

		Yii::$app->view->registerLinkTag(['rel' => 'alternate', 'href' => Url::to(['albumpdf', 'artist' => $tracks[0]->artist->url, 'year' => $tracks[0]->album->year, 'album' => $tracks[0]->album->url], true), 'type' => 'application/pdf', 'title' => 'PDF']);
		if ($tracks[0]->album->image)
			Yii::$app->view->registerMetaTag(['property' => 'og:image', 'content' => Url::to(['cover', 'artist' => $tracks[0]->artist->url, 'year' => $tracks[0]->album->year, 'album' => $tracks[0]->album->url, 'size' => 'cover'], true)]);
		Yii::$app->view->registerMetaTag(['property' => 'og:type', 'content' => 'music.album']);

		return ['3_album', $tracks];
	}

	private function redirectIfNotUrl(string $page, array $data): bool {
		if ($data[0]->artist->url !== $this->artist || $data[0]->album->url !== $this->album)
			$this->redirect([$page, 'artist' => $data[0]->artist->url, 'year' => $data[0]->album->year, 'album' => $data[0]->album->url], 301)->send();
		return true;
	}
}
