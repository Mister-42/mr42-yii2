<?php
namespace app\controllers;
use Yii;
use app\models\Image;
use app\models\music\{Collection, Lyrics1Artists, Lyrics2Albums, Lyrics3Tracks};
use yii\helpers\ArrayHelper;
use yii\web\{NotFoundHttpException, Response};

class MusicController extends \yii\web\Controller {
	public $view;
	public $data;
	public $artist;
	public $year;
	public $album;
	public $size;
	public $lastModified;

	public function behaviors(): array {
		if (ArrayHelper::isIn($this->action->id, ['collection', 'collection-cover']))
			return [];

		$this->artist = Yii::$app->request->get('artist');
		$this->year = Yii::$app->request->get('year');
		$this->album = Yii::$app->request->get('album');
		$this->size = Yii::$app->request->get('size');

		if ($this->artist && $this->year && $this->album) :
			list($this->view, $this->data) = $this->getAlbum();
			$this->lastModified = Lyrics3Tracks::getLastModified($this->artist, $this->year, $this->album);
		elseif ($this->artist) :
			list($this->view, $this->data) = $this->getArtist();
			$this->lastModified = Lyrics2Albums::getLastModified($this->artist);
		else :
			$this->view = '1_index';
			$this->data = Lyrics1Artists::artistsList();
			$this->lastModified = Lyrics1Artists::getLastModified();
		endif;

		return [
			[
				'class' => \yii\filters\HttpCache::class,
				'enabled' => !YII_DEBUG,
				'etagSeed' => function() { return serialize([phpversion(), Yii::$app->user->id, $this->lastModified]); },
				'lastModified' => function() { return $this->lastModified; },
				'only' => ['index', 'albumpdf', 'albumcover'],
			],
		];
	}

	public function actionCollection(): string {
		return $this->render('collection', [
			'model' => new Collection(),
		]);
	}

	public function actionCollectionCover(int $id): Response {
		$album = Collection::find()->where(['id' => $id])->one();
		if (!$album || !$album->image) :
			$image = file_get_contents(Yii::getAlias('@assetsroot/images/nocdcover.png'));
			return Yii::$app->response->sendContentAsFile($image, 'nocdcover.png', ['mimeType' => 'image/png', 'inline' => true]);
		endif;

		return Yii::$app->response->sendContentAsFile($album->image, "{$id}.jpg", ['mimeType' => 'image/jpeg', 'inline' => true]);
	}

	public function actionLyrics(): string {
		Yii::$app->view->registerMetaTag(['name' => 'google', 'content' => 'notranslate']);
		return $this->render($this->view, [
			'data' => $this->data,
		]);
	}

	public function actionAlbumpdf(): Response {
		$pdf = Lyrics2Albums::buildPdf($this->data[0]->album, $this->renderPartial('albumPdf', ['tracks' => $this->data]));
		return Yii::$app->response->sendFile($pdf, implode(' - ', [$this->data[0]->artist->url, $this->data[0]->album->year, $this->data[0]->album->url]).'.pdf');
	}

	public function actionAlbumcover(): Response {
		if (!ArrayHelper::isIn($this->size, [100, 500, 800]))
			throw new NotFoundHttpException('Cover not found.');

		list($fileName, $image) = Lyrics2Albums::getCover($this->size, $this->data);
		return Yii::$app->response->sendContentAsFile($image, $fileName, ['mimeType' => 'image/jpeg', 'inline' => true]);
	}

	private function getArtist(): array {
		$albums = Lyrics2Albums::albumsList($this->artist);

		if (count($albums) === 0)
			throw new NotFoundHttpException('Artist not found.');

		if ($albums[0]->artist->url !== $this->artist)
			$this->redirect(["/{$this->module->requestedRoute}", 'artist' => $albums[0]->artist->url], 301)->send();

		return ['2_artist', $albums];
	}

	private function getAlbum(): array {
		$tracks = Lyrics3Tracks::tracksList($this->artist, $this->year, $this->album);

		if (!ArrayHelper::keyExists(0, $tracks))
			throw new NotFoundHttpException('Album not found.');

		if ($tracks[0]->artist->url !== $this->artist || $tracks[0]->album->url !== $this->album)
			$this->redirect(["/{$this->module->requestedRoute}", 'artist' => $tracks[0]->artist->url, 'year' => $tracks[0]->album->year, 'album' => $tracks[0]->album->url, 'size' => $this->size], 301)->send();

		return ['3_album', $tracks];
	}
}
