<?php
namespace app\commands;
use Yii;
use app\models\{Console, Image, Video, Webrequest};
use app\models\music\{Lyrics1Artists, Lyrics2Albums, Lyrics3Tracks};
use app\models\user\{Profile, User};
use yii\helpers\ArrayHelper;

/**
 * Handles all actions related to music.
 */
class LyricsController extends \yii\console\Controller {
	const ALBUM_IMAGE_DIMENSIONS = 1000;

	public $defaultAction = 'index';

	/**
	 * Perform image & PDF actions.
	 */
	public function actionIndex() {
		$this->actionAlbumImage();
		$this->actionAlbumPdf();
	}

	/**
	 * Resizes all album covers to the default dimensions if they exceed this limit.
	 */
	public function actionAlbumImage() {
		$x = 0;
		$count = (int) Lyrics2Albums::find()->count();
		Console::startProgress(0, $count, 'Processing Images: ');
		foreach (Lyrics1Artists::albumsList() as $artist) :
			foreach ($artist->albums as $album) :
				Console::updateProgress(++$x, $count);
				list($width, $height, $type) = ($album->image) ? getimagesizefromstring($album->image) : [0, 0, 0];
				if ($width === self::ALBUM_IMAGE_DIMENSIONS && $height === self::ALBUM_IMAGE_DIMENSIONS && $type === IMAGETYPE_JPEG && !is_null($album->image_color))
					continue;

				Console::write($artist->name, [Console::FG_PURPLE], 3);
				Console::write($album->year, [Console::FG_GREEN]);
				Console::write($album->name, [Console::FG_GREEN], 8);

				if (!$album->image) :
					Console::write('Missing', [Console::BOLD, Console::FG_RED, CONSOLE::BLINK]);
					Console::newLine();
					continue;
				endif;

				Console::write("{$width}x{$height}", [$width === self::ALBUM_IMAGE_DIMENSIONS ? Console::FG_GREEN : Console::FG_RED], 2);

				if (($width > self::ALBUM_IMAGE_DIMENSIONS && $height > self::ALBUM_IMAGE_DIMENSIONS) || $type !== IMAGETYPE_JPEG) :
					if ($width >= self::ALBUM_IMAGE_DIMENSIONS && $height >= self::ALBUM_IMAGE_DIMENSIONS) :
						$album->image = Image::resize($album->image, self::ALBUM_IMAGE_DIMENSIONS);
						$album->image_color = Image::getAverageImageColor($album->image);
						$album->save();
						list($width, $height) = getimagesizefromstring($album->image);
						Console::write("{$width}x{$height}", [Console::BOLD, Console::FG_GREEN]);
					endif;
					Console::newLine();
					continue;
				elseif ($album->image && is_null($album->image_color)) :
					$album->image_color = Image::getAverageImageColor($album->image);
					$album->save();
				endif;

				Console::write("OK", [Console::BOLD, Console::FG_GREEN]);
				Console::newLine();
			endforeach;
		endforeach;

		Console::endProgress(true);
	}

	/**
	 * Builds all albums PDF files, unless already cached and up-to-date.
	 */
	public function actionAlbumPdf() {
		$x = 0;
		$count = (int) Lyrics2Albums::find()->count();
		Console::startProgress(0, $count, 'Processing PDFs: ');
		foreach (Lyrics1Artists::albumsList() as $artist) :
			foreach ($artist->albums as $album) :
				Console::updateProgress(++$x, $count);
				if (!$album->active || $fileName = Lyrics2Albums::buildPdf($album))
					continue;

				Console::write($artist->name, [Console::FG_PURPLE], 3);
				Console::write($album->year, [Console::FG_GREEN]);
				Console::write($album->name, [Console::FG_GREEN], 8);

				if (!$fileName) :
					Console::writeError("ERROR!", [Console::BOLD, Console::FG_RED, CONSOLE::BLINK]);
					continue;
				endif;

				Console::write(Yii::$app->formatter->asShortSize(filesize($fileName), 2), [Console::BOLD, Console::FG_GREEN]);
				Console::newLine();
			endforeach;
		endforeach;

		Console::endProgress(true);
	}

	/**
	 * Checking status of album playlists.
	 */
	public function actionPlaylists() {
		$x = 0;
		foreach (Lyrics1Artists::albumsList() as $artist) :
			foreach ($artist->albums as $album) :
				if (isset($album->playlist_id))
					$data[] = ['id' => $album->playlist_id, 'artist' => $artist->name, 'year' => $album->year, 'name' => $album->name];

				if (!isset($data) || (++$x !== count($artist->albums) && count($data) < 50))
					continue;

				$response = Webrequest::getYoutubeApi(implode(',', ArrayHelper::getColumn($data, 'id')), 'playlists');
				if (!$response->isOK || $response->data['pageInfo']['totalResults'] === 0) :
					Console::writeError('Error', [Console::BOLD, Console::FG_RED, CONSOLE::BLINK]);
					return self::EXIT_CODE_ERROR;
				endif;
				$response = ArrayHelper::index($response->data['items'], 'id');

				foreach ($data as $albumData) :
					if (!ArrayHelper::keyExists($albumData['id'], $response, false) || ArrayHelper::getValue($response, "{$albumData['id']}.status.privacyStatus") !== 'public') :
						Console::write($albumData['artist'], [Console::FG_PURPLE], 3);
						Console::write($albumData['year'], [Console::FG_GREEN]);
						Console::write($albumData['name'], [Console::FG_GREEN], 8);

						if (!ArrayHelper::keyExists($albumData['id'], $response, false)) :
							Console::writeError('Not Found', [Console::BOLD, Console::FG_RED, CONSOLE::BLINK]);
						elseif (ArrayHelper::getValue($response, "{$albumData['id']}.status.privacyStatus") !== 'public'):
							Console::writeError('Not Public', [Console::BOLD, Console::FG_RED, CONSOLE::BLINK]);
						endif;

						Console::newLine();
						continue;
					endif;
				endforeach;

				unset($data, $response);
			endforeach;
		endforeach;

		Console::write('Completed checking playlists', [Console::BOLD, Console::FG_GREEN]);
		Console::newLine();
		return self::EXIT_CODE_NORMAL;
	}

	/**
	 * Checking status of tracks videos.
	 */
	public function actionVideos() {
		$x = 0;
		$query = Lyrics3Tracks::find()->where(['not', ['video_id' => null]])->all();
		foreach ($query as $track) :
			$data[] = ['id' => $track->video_id, 'name' => $track->name];
			if (++$x !== count($query) && count($data) < 50)
				continue;

			$response = Webrequest::getYoutubeApi(implode(',', ArrayHelper::getColumn($data, 'id')), 'videos');
			if (!$response->isOK || $response->data['pageInfo']['totalResults'] === 0) :
				Console::writeError('Error: Could not get response from server', [Console::BOLD, Console::FG_RED, CONSOLE::BLINK]);
				return self::EXIT_CODE_ERROR;
			endif;
			$response = ArrayHelper::index($response->data['items'], 'id');

			foreach ($data as $trackData) :
				if (!ArrayHelper::keyExists($trackData['id'], $response, false) || !ArrayHelper::getValue($response, "{$trackData['id']}.status.embeddable")) :
					Console::write($trackData['name'], [Console::FG_PURPLE], 5);
					Console::write(video::getUrl('youtube', $trackData['id']), [Console::FG_PURPLE], 5);

					if (!ArrayHelper::keyExists($trackData['id'], $response, false)) :
						Console::writeError('Not Found', [Console::BOLD, Console::FG_RED, CONSOLE::BLINK]);
					elseif (!ArrayHelper::getValue($response, "{$trackData['id']}.status.embeddable")) :
						Console::write('Not embeddable', [Console::BOLD, Console::FG_RED, CONSOLE::BLINK]);
					endif;

					Console::newLine();
					continue;
				endif;
			endforeach;
			unset($data, $response);
		endforeach;

		Console::write('Completed checking videos', [Console::BOLD, Console::FG_GREEN]);
		Console::newLine();
		return self::EXIT_CODE_NORMAL;
	}
}
