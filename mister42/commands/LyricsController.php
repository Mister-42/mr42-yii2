<?php
namespace app\commands;
use Yii;
use app\models\{Console, Webrequest};
use app\models\lyrics\{Lyrics1Artists, Lyrics2Albums, Lyrics3Tracks};
use yii\console\Controller;
use yii\helpers\ArrayHelper;

/**
 * Handles all actions related to lyrics.
 */
class LyricsController extends Controller {
	const ALBUM_IMAGE_DIMENSIONS = 1000;

	public $defaultAction = 'index';

	/**
	 * Perform all actions.
	 */
	public function actionIndex() {
		self::actionAlbumImage();
		self::actionAlbumPdf();
	}

	/**
	 * Resizes all album covers to the default dimensions if they exceed this limit.
	 */
	public function actionAlbumImage() {
		foreach (Lyrics1Artists::albumsList() as $artist) :
			foreach ($artist->albums as $album) :
				list($width, $height) = getimagesizefromstring($album->image);
				$exif = exif_read_data("data://image/jpeg;base64,".base64_encode($album->image));
				if ($width === self::ALBUM_IMAGE_DIMENSIONS && $height === self::ALBUM_IMAGE_DIMENSIONS && empty($exif['SectionsFound']) && $exif['MimeType'] === 'image/jpeg' && !is_null($album->image_color)) :
					continue;
				endif;

				Console::write($artist->name, [Console::FG_PURPLE], 3);
				Console::write($album->year, [Console::FG_GREEN]);
				Console::write($album->name, [Console::FG_GREEN], 8);

				if (!$album->image) :
					Console::write('Missing', [Console::BOLD, Console::FG_RED]);
					Console::newLine();
					continue;
				endif;

				Console::write("{$width}x{$height}", [$width === self::ALBUM_IMAGE_DIMENSIONS ? Console::FG_GREEN : Console::FG_RED], 2);

				if (($width > self::ALBUM_IMAGE_DIMENSIONS && $height > self::ALBUM_IMAGE_DIMENSIONS) || !empty($exif['SectionsFound']) || $exif['MimeType'] !== 'image/jpeg') :
					if ($width >= self::ALBUM_IMAGE_DIMENSIONS && $height >= self::ALBUM_IMAGE_DIMENSIONS) :
						$album->image = (Lyrics2Albums::getCover(self::ALBUM_IMAGE_DIMENSIONS, $album))[1];
						$album->image_color = self::getAverageImageColor($album->image);
						$album->save();
						list($width, $height) = getimagesizefromstring($album->image);
						Console::write("{$width}x{$height}", [Console::BOLD, Console::FG_GREEN]);
					endif;
					Console::newLine();
					continue;
				elseif ($album->image && is_null($album->image_color)) :
					$album->image_color = self::getAverageImageColor($album->image);
					$album->save();
				endif;

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
		foreach (Lyrics1Artists::albumsList() as $artist) :
			foreach ($artist->albums as $album) :
				if (!$album->active) :
					continue;
				endif;

				Console::write($artist->name, [Console::FG_PURPLE], 3);
				Console::write($album->year, [Console::FG_GREEN]);
				Console::write($album->name, [Console::FG_GREEN], 8);

				$fileName = Lyrics2Albums::buildPdf($album, $this->renderPartial('@app/views/lyrics/albumPdf', ['tracks' => $album->tracks]));
				if (!$fileName) :
					Console::writeError("ERROR!", [Console::BOLD, Console::FG_RED]);
					continue;
				endif;

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
		foreach (Lyrics1Artists::albumsList() as $artist) :
			foreach ($artist->albums as $album) :
				if (isset($album->playlist_id)) :
					$data[] = ['id' => $album->playlist_id, 'artist' => $artist->name, 'year' => $album->year, 'name' => $album->name];
				endif;

				if (!isset($data) || (++$x !== count($artist->albums) && count($data) < 50)) :
					continue;
				else :
					$response = Webrequest::getYoutubeApi(implode(',', ArrayHelper::getColumn($data, 'id')), 'playlists');
					if (!$response->isOK || $response->data['pageInfo']['totalResults'] === 0) :
						Console::writeError('Error', [Console::BOLD, Console::FG_RED]);
						return self::EXIT_CODE_ERROR;
					else :
						$response = ArrayHelper::index($response->data['items'], 'id');
					endif;

					foreach ($data as $albumData) :
						Console::write($albumData['artist'], [Console::FG_PURPLE], 3);
						Console::write($albumData['year'], [Console::FG_GREEN]);
						Console::write($albumData['name'], [Console::FG_GREEN], 8);

						if (!ArrayHelper::keyExists($albumData['id'], $response, false)) :
							Console::writeError('Not Found', [Console::BOLD, Console::FG_RED]);
							continue;
						endif;

						Console::write(ArrayHelper::getValue($response, "{$albumData['id']}.contentDetails.itemCount")." found", [Console::BOLD, Console::FG_GREEN], 2);
						Console::write(ArrayHelper::getValue($response, "{$albumData['id']}.status.privacyStatus"), [Console::BOLD, Console::FG_GREEN], 0);
						Console::newLine();
					endforeach;

					unset($data, $response);
				endif;
			endforeach;
		endforeach;
		return self::EXIT_CODE_NORMAL;
	}

	/**
	 * Checking status of tracks video.
	 */
	public function actionTrackVideo() {
		$query = Lyrics3Tracks::find()->where(['not', ['video_id' => null]])->all();
		foreach ($query as $track) :
			$data[] = ['id' => $track->video_id, 'name' => $track->name];
			if (++$x !== count($query) && count($data) < 50) :
				continue;
			else :
				$response = Webrequest::getYoutubeApi(implode(',', ArrayHelper::getColumn($data, 'id')), 'videos');
				if (!$response->isOK || $response->data['pageInfo']['totalResults'] === 0) :
					Console::writeError('Error', [Console::BOLD, Console::FG_RED]);
					return self::EXIT_CODE_ERROR;
				else :
					$response = ArrayHelper::index($response->data['items'], 'id');
				endif;

				foreach ($data as $trackData) :
					Console::write($trackData['name'], [Console::FG_PURPLE], 5);

					if (!ArrayHelper::keyExists($trackData['id'], $response, false)) :
						Console::writeError('Not Found', [Console::BOLD, Console::FG_RED]);
						continue;
					endif;

					ArrayHelper::getValue($response, "{$trackData['id']}.status.embeddable")
						? Console::write('Embeddable', [Console::FG_GREEN], 2)
						: Console::write('Not embeddable', [Console::BOLD, Console::FG_RED], 2);
					Console::write(ArrayHelper::getValue($response, "{$trackData['id']}.snippet.title"), [Console::FG_GREEN]);
					Console::newLine();
				endforeach;
				unset($data, $response);
			endif;
		endforeach;
		return self::EXIT_CODE_NORMAL;
	}

	private function getAverageImageColor(string $image): string {
		$i = imagecreatefromstring($image);
		list($width, $height) = getimagesizefromstring($image);
		for ($x = 0; $x < $width; $x++) :
			for ($y = 0; $y < $height; $y++) :
				$rgb = imagecolorat($i, $x, $y);
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;
				$rTotal += $r;
				$gTotal += $g;
				$bTotal += $b;
				$total++;
			endfor;
		endfor;
		return sprintf('#%02X%02X%02X', $rTotal / $total, $gTotal / $total, $bTotal / $total); 
	}
}
