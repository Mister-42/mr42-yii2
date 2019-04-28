<?php
namespace app\commands;
use Yii;
use app\models\{Console, Image, Video};
use app\models\music\{Lyrics1Artists, Lyrics2Albums, Lyrics3Tracks};
use app\models\user\{Profile, User};

/**
 * Handles all actions related to music.
 */
class LyricsController extends \yii\console\Controller {
	const ALBUM_IMAGE_DIMENSIONS = 1000;

	public $defaultAction = 'index';

	/**
	 * Perform image & PDF actions.
	 */
	public function actionIndex(): void {
		$this->actionAlbumImage();
		$this->actionAlbumPdf();
	}

	/**
	 * Resizes all album covers to the default dimensions if they exceed this limit.
	 */
	public function actionAlbumImage(): void {
		$x = 0;
		$count = (int) Lyrics2Albums::find()->count();
		Console::startProgress(0, $count, 'Processing Images: ');
		foreach (Lyrics1Artists::albumsList() as $artist) :
			foreach ($artist->albums as $album) :
				Console::updateProgress(++$x, $count);
				[$width, $height, $type] = ($album->image) ? getimagesizefromstring($album->image) : [0, 0, 0];
				if ($width === self::ALBUM_IMAGE_DIMENSIONS && $height === self::ALBUM_IMAGE_DIMENSIONS && $type === IMAGETYPE_JPEG && $album->image_color !== null)
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
						[$width, $height] = getimagesizefromstring($album->image);
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
	public function actionAlbumPdf(): void {
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
	 * Checking status of album playlists and track videos.
	 */
	public function actionVideos(): int {
		$video = new Video();
		foreach (['playlists', 'videos'] as $type) :
			if ($type === 'playlists') :
				$query = Lyrics1Artists::find()->orderBy(['name' => SORT_ASC])->with(['albums' => function ($q) { $q->where(['not', ['playlist_source' => null, 'playlist_id' => null]]); }]);
				foreach ($query->each() as $artist)
					foreach ($artist->albums as $album)
						$data[$album->playlist_source][] = ['id' => $album->playlist_id, 'artist' => $artist->name, 'year' => $album->year, 'name' => $album->name];
			elseif ($type === 'videos') :
				$query = Lyrics3Tracks::find()->where(['not', ['video_source' => null, 'video_id' => null]]);
				foreach ($query->each() as $track)
					$data[$track->video_source][] = ['id' => $track->video_id, 'name' => $track->name];
			endif;

			foreach ($data as $source => $payload) :
				$x = 0;
				$function = implode(['check', ucfirst($source)]);

				foreach ($payload as $id) :
					$ids[] = $id;
					if (!isset($ids) || (++$x !== count($data[$source]) && count($ids) < 50))
						continue;

					$result[$source] = $video->$function($ids, $type);
					unset($ids);
				endforeach;
			endforeach;

			if ((bool) array_product($result) === true) :
				Console::write("Completed checking {$type}", [Console::BOLD, Console::FG_GREEN]);
				Console::newLine();
			endif;
			unset($data);
		endforeach;

		if ((bool) array_product($result) === true)
			return self::EXIT_CODE_NORMAL;
		return self::EXIT_CODE_ERROR;
	}
}
