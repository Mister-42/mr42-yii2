<?php
namespace app\commands;
use Yii;
use app\models\{Console, Youtube};
use app\models\lyrics\{Lyrics1Artists, Lyrics2Albums, Lyrics3Tracks};
use yii\console\Controller;
use yii\helpers\Url;

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
			foreach($artist->albums as $album) :
				if (!$album->playlist_source && !$album->playlist_id)
					continue;
				Console::write($artist->name, [Console::FG_PURPLE], 3);
				Console::write($album->year, [Console::FG_GREEN]);
				Console::write($album->name, [Console::FG_GREEN], 8);

				$response = Youtube::getApiRequest($album->playlist_id, 'playlists');
				if (!$response->isOK || $response->data['pageInfo']['totalResults'] === 0) {
					Console::writeError('404 - Not Found', [Console::BOLD, Console::FG_RED]);
					continue;
				}

				if ($response->data['pageInfo']['totalResults'] > 0) {
					Console::write("{$response->data['items'][0]['contentDetails']['itemCount']} found", [Console::BOLD, Console::FG_GREEN], 2);
					if (!$response->data['items'][0])
						Console::writeError('Not found', [Console::BOLD, Console::FG_RED]);
						continue;
				}
				Console::write($response->data['items'][0]['status']['privacyStatus'], [Console::BOLD, Console::FG_GREEN], 0);
				Console::newLine();
			endforeach;
		endforeach;

		return self::EXIT_CODE_NORMAL;
	}

	/**
	 * Checking status of tracks video.
	*/
	public function actionTrackVideo() {
		$data = Lyrics3Tracks::find()->where(['not', ['video_source' => null]])->andWhere(['not', ['video_id' => null]])->all();
		foreach($data as $track) :
			Console::write($track->name, [Console::FG_PURPLE], 5);

			$response = Youtube::getApiRequest($track->video_id, 'videos');
			if (!$response->isOK || $response->data['pageInfo']['totalResults'] === 0) {
				Console::writeError('404 - Not Found', [Console::BOLD, Console::FG_RED]);
				continue;
			}

			$response->data['items'][0]['status']['embeddable']
				? Console::write('Embeddable', [Console::FG_GREEN], 2)
				: Console::write('Not embeddable', [Console::BOLD, Console::FG_RED], 2);
			Console::write($response->data['items'][0]['snippet']['title'], [Console::FG_GREEN]);
			Console::newLine();
		endforeach;

		return self::EXIT_CODE_NORMAL;
	}
}
