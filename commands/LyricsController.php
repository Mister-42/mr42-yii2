<?php
namespace app\commands;
use Yii;
use app\models\{Console, Youtube};
use app\models\lyrics\{Lyrics1Artists, Lyrics2Albums, Lyrics3Tracks};
use yii\console\Controller;
use yii\helpers\ArrayHelper;

/**
 * Handles all actions related to lyrics.
 */
class LyricsController extends Controller {
	public $defaultAction = 'index';

	/**
	 * Perform all actions.
	*/
	public function actionIndex() {
		self::actionAlbumImage();
		self::actionAlbumPdf();
	}

	/**
	 * Resizes all album covers to 999x999 if they exceed this limit.
	*/
	public function actionAlbumImage() {
		foreach(Lyrics1Artists::albumsList() as $artist) :
			foreach($artist->albums as $album) :
				if (!$album->image)
					continue;
				Console::write($artist->name, [Console::FG_PURPLE], 3);
				Console::write($album->year, [Console::FG_GREEN]);
				Console::write($album->name, [Console::FG_GREEN], 8);

				list($width, $height) = getimagesizefromstring($album->image);
				Console::write("{$width}x{$height}", [Console::FG_GREEN], 2);
				if ($width > 999 && $height > 999) {
					$album->image = (Lyrics2Albums::getCover(999, $album))[1];
					$album->save();
					list($width, $height) = getimagesizefromstring($album->image);
					Console::write("{$width}x{$height}", [Console::BOLD, Console::FG_GREEN]);
					Console::newLine();
					continue;
				}

				Console::write("OK", [Console::BOLD, Console::FG_GREEN]);
				Console::newLine();
			endforeach;
		endforeach;

		return self::EXIT_CODE_NORMAL;
	}

	/**
	 * Builds all albums PDF files, unless already cached and up-to-date.
	*/
	public function actionAlbumPdf() {
		foreach(Lyrics1Artists::albumsList() as $artist) :
			foreach($artist->albums as $album) :
				if (!$album->active)
					continue;
				Console::write($artist->name, [Console::FG_PURPLE], 3);
				Console::write($album->year, [Console::FG_GREEN]);
				Console::write($album->name, [Console::FG_GREEN], 8);

				$fileName = Lyrics2Albums::buildPdf($album, $this->renderPartial('@app/views/lyrics/albumPdf', ['tracks' => $album->tracks]));
				if (!$fileName) {
					Console::writeError("ERROR!", [Console::BOLD, Console::FG_RED]);
					continue;
				}

				Console::write(Yii::$app->formatter->asShortSize(filesize($fileName), 2), [Console::BOLD, Console::FG_GREEN]);
				Console::newLine();
			endforeach;
		endforeach;

		return self::EXIT_CODE_NORMAL;
	}

	/**
	 * Checking status of album playlist.
	*/
	public function actionAlbumPlaylist() {
		foreach(Lyrics1Artists::albumsList() as $artist) :
			$x = 0;
			foreach($artist->albums as $album) :
				if (isset($album->playlist_id))
					$data[] = ['id' => $album->playlist_id, 'artist' => $artist->name, 'year' => $album->year, 'name' => $album->name];

				$x++;
				if (!isset($data) || ($x !== count($artist->albums) && count($data) < 50))
					continue;
				else {
					$response = Youtube::getApiRequest(implode(',', ArrayHelper::getColumn($data, 'id')), 'playlists');
					if (!$response->isOK || $response->data['pageInfo']['totalResults'] === 0) {
						Console::writeError('Error', [Console::BOLD, Console::FG_RED]);
						return self::EXIT_CODE_ERROR;
					} else
						$response = ArrayHelper::index($response->data['items'], 'id');

					foreach ($data as $albumData) :
						Console::write($albumData['artist'], [Console::FG_PURPLE], 3);
						Console::write($albumData['year'], [Console::FG_GREEN]);
						Console::write($albumData['name'], [Console::FG_GREEN], 8);

						if (!$response[$albumData['id']]) {
							Console::writeError('Not Found', [Console::BOLD, Console::FG_RED]);
							continue;
						}

						Console::write("{$response[$albumData['id']]['contentDetails']['itemCount']} found", [Console::BOLD, Console::FG_GREEN], 2);
						Console::write($response[$albumData['id']]['status']['privacyStatus'], [Console::BOLD, Console::FG_GREEN], 0);
						Console::newLine();
					endforeach;

					unset($data, $response);
				}
			endforeach;
		endforeach;

		return self::EXIT_CODE_NORMAL;
	}

	/**
	 * Checking status of tracks video.
	*/
	public function actionTrackVideo() {
		$x = 0;
		$query = Lyrics3Tracks::find()->where(['not', ['video_id' => null]])->all();
		foreach($query as $track) :
			$data[] = ['num' => $x, 'id' => $track->video_id, 'name' => $track->name];
			if (++$x !== count($query) && count($data) < 50)
				continue;
			else {
				$response = Youtube::getApiRequest(implode(',', ArrayHelper::getColumn($data, 'id')), 'videos');
				if (!$response->isOK || $response->data['pageInfo']['totalResults'] === 0) {
					Console::writeError('Error', [Console::BOLD, Console::FG_RED]);
					return self::EXIT_CODE_ERROR;
				} else
					$response = ArrayHelper::index($response->data['items'], 'id');

				foreach ($data as $trackData) :
					Console::write($trackData['num'], [Console::FG_PURPLE]);
					Console::write($trackData['name'], [Console::FG_PURPLE], 5);

					if (!$response[$trackData['id']]) {
						Console::writeError('Not Found', [Console::BOLD, Console::FG_RED]);
						continue;
					}

					$response[$trackData['id']]['status']['embeddable']
						? Console::write('Embeddable', [Console::FG_GREEN], 2)
						: Console::write('Not embeddable', [Console::BOLD, Console::FG_RED], 2);
					Console::write($response[$trackData['id']]['snippet']['title'], [Console::FG_GREEN]);
					Console::newLine();
				endforeach;

				unset($data, $response);
			}
		endforeach;

		return self::EXIT_CODE_NORMAL;
	}
}
