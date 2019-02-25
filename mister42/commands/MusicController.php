<?php
namespace app\commands;
use Yii;
use app\models\{Console, Image, Video, Webrequest};
use app\models\music\{Collection, Lyrics1Artists, Lyrics2Albums, Lyrics3Tracks};
use app\models\user\{Profile, User};
use yii\console\Controller;
use yii\helpers\ArrayHelper;

/**
 * Handles all actions related to music.
 */
class MusicController extends Controller {
	const ALBUM_IMAGE_DIMENSIONS = 1000;

	public $defaultAction = 'index';

	/**
	 * Perform image & PDF actions.
	 */
	public function actionIndex() {
		$this->actionLyricsAlbumImage();
		$this->actionLyricsAlbumPdf();
	}

	/**
	 * Retrieves and stores Discogs Collection & Wantlist
	 */
	public function actionCollection(): int {
		$discogs = new Collection();
		foreach (User::find()->where(['blocked_at' => null])->all() as $user) :
			$profile = Profile::findOne(['user_id' => $user->id]);
			if (isset($profile->discogs) && isset($profile->discogs_token)) :
				foreach (['collection', 'wishlist'] as $action) :
					if (!$url = $this->getDiscogsUrl($action, $profile))
						continue;

					$response = Webrequest::getDiscogsApi("{$url}?".http_build_query(['token' => $profile->discogs_token]));
					if (!$response->isOK)
						continue;
					$ids = $discogs->saveCollection($profile->user_id, $response->data[($action === 'collection') ? 'releases' : 'wants'], $action);

					for ($x = 2; $x < (int) ArrayHelper::getValue($response->data, 'pagination.pages'); $x++) :
						$response = Webrequest::getDiscogsApi("{$url}?".http_build_query(['page' => $x, 'token' => $profile->discogs_token]));
						if (!$response->isOK)
							continue;
						$subids = $discogs->saveCollection($profile->user_id, $response->data[($action === 'collection') ? 'releases' : 'wants'], $action);
						$ids = array_merge($ids, $subids);
					endfor;
					Collection::deleteAll(['AND', ['user_id' => $profile->user_id], ['NOT IN', 'id', $ids], ['status' => $action]]);
				endforeach;
			endif;
		endforeach;

		return self::EXIT_CODE_NORMAL;
	}

	/**
	 * Resizes all album covers to the default dimensions if they exceed this limit.
	 */
	public function actionLyricsAlbumImage() {
		$x = 0;
		$count = (int) Lyrics2Albums::find()->count();
		Console::startProgress(0, $count, 'Processing Images: ');
		foreach (Lyrics1Artists::albumsList() as $artist) :
			foreach ($artist->albums as $album) :
				Console::updateProgress(++$x, $count);
				if ($album->image)
					list($width, $height, $type) = getimagesizefromstring($album->image);
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
						$album->image_color = $this->getAverageImageColor($album->image);
						$album->save();
						list($width, $height) = getimagesizefromstring($album->image);
						Console::write("{$width}x{$height}", [Console::BOLD, Console::FG_GREEN]);
					endif;
					Console::newLine();
					continue;
				elseif ($album->image && is_null($album->image_color)) :
					$album->image_color = $this->getAverageImageColor($album->image);
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
	public function actionLyricsAlbumPdf() {
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
	 * Checking status of album playlist.
	 */
	public function actionLyricsPlaylist() {
		$x = 0;
		foreach (Lyrics1Artists::albumsList() as $artist) :
			foreach ($artist->albums as $album) :
				if (isset($album->playlist_id))
					$data[] = ['id' => $album->playlist_id, 'artist' => $artist->name, 'year' => $album->year, 'name' => $album->name];

				if (!isset($data) || (++$x !== count($artist->albums) && count($data) < 50)) :
					continue;
				else :
					$response = Webrequest::getYoutubeApi(implode(',', ArrayHelper::getColumn($data, 'id')), 'playlists');
					if (!$response->isOK || $response->data['pageInfo']['totalResults'] === 0) :
						Console::writeError('Error', [Console::BOLD, Console::FG_RED, CONSOLE::BLINK]);
						return self::EXIT_CODE_ERROR;
					else :
						$response = ArrayHelper::index($response->data['items'], 'id');
					endif;

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
				endif;
			endforeach;
		endforeach;

		Console::write('Completed processing playlists', [Console::BOLD, Console::FG_RED]);
		Console::newLine();
		return self::EXIT_CODE_NORMAL;
	}

	/**
	 * Checking status of tracks video.
	 */
	public function actionLyricsVideo() {
		$x = 0;
		$query = Lyrics3Tracks::find()->where(['not', ['video_id' => null]])->all();
		foreach ($query as $track) :
			$data[] = ['id' => $track->video_id, 'name' => $track->name];
			if (++$x !== count($query) && count($data) < 50) :
				continue;
			else :
				$response = Webrequest::getYoutubeApi(implode(',', ArrayHelper::getColumn($data, 'id')), 'videos');
				if (!$response->isOK || $response->data['pageInfo']['totalResults'] === 0) :
					Console::writeError('Error: Could not get response from server', [Console::BOLD, Console::FG_RED, CONSOLE::BLINK]);
					return self::EXIT_CODE_ERROR;
				else :
					$response = ArrayHelper::index($response->data['items'], 'id');
				endif;

				foreach ($data as $trackData) :
					if (!ArrayHelper::keyExists($trackData['id'], $response, false) || !ArrayHelper::getValue($response, "{$trackData['id']}.status.embeddable")) :
						Console::write($trackData['name'], [Console::FG_PURPLE], 5);
						Console::write(video::getUrl('youtube', $trackData['id']), [Console::FG_PURPLE], 5);

						if (!ArrayHelper::keyExists($trackData['id'], $response, false)) :
							Console::writeError('Not Found', [Console::BOLD, Console::FG_RED, CONSOLE::BLINK]);
						elseif (!ArrayHelper::getValue($response, "{$trackData['id']}.status.embeddable")) :
							Console::write('Not embeddable', [Console::BOLD, Console::FG_RED, CONSOLE::BLINK]);
						endif;

						continue;
					endif;
				endforeach;
				unset($data, $response);
			endif;
		endforeach;

		Console::write('Completed processing videos', [Console::BOLD, Console::FG_RED]);
		Console::newLine();
		return self::EXIT_CODE_NORMAL;
	}

	private function getAverageImageColor(string $image): string {
		$i = imagecreatefromstring($image);
		$rTotal = $gTotal = $bTotal = $total = 0;
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

	private function getDiscogsUrl(string $action, Profile $profile) : ?string {
		if ($action === 'collection') :
			$response = Webrequest::getDiscogsApi("users/{$profile->discogs}/collection/folders?".http_build_query(['token' => $profile->discogs_token]));
			if (!$response->isOK)
				return null;
			return "/users/{$profile->discogs}/collection/folders/{$response->data['folders'][1]['id']}/releases";
		endif;

		return "/users/{$profile->discogs}/wants";
	}
}
