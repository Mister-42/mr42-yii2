<?php
namespace app\commands;
use Yii;
use app\models\lyrics\{Lyrics1Artists, Lyrics2Albums};
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Image management.
 */
class ImageController extends Controller {
	const TABSIZE = 8;
	public $defaultAction = 'albums';

	/**
	 * Resizes all album covers to 999x999 if they exceed this limit.
	*/
	public function actionAlbums() {
		foreach(Lyrics1Artists::albumsList() as $artist) :
			$artistName = $this->ansiFormat($artist->name, Console::FG_PURPLE);
			foreach($artist->albums as $album) :
				if (!$album->image)
					continue;
				$albumYear = $this->ansiFormat($album->year, Console::FG_GREEN);
				$albumName = $this->ansiFormat($album->name, Console::FG_GREEN);
				$this->stdout($artistName);
				for($x=0; $x<(3 - intdiv(mb_strlen($artist->name), Self::TABSIZE)); $x++)
					$this->stdout("\t");
				$this->stdout("$albumYear\t\t$albumName");
				for($x=0; $x<(8 - intdiv(mb_strlen($album->name), Self::TABSIZE)); $x++)
					$this->stdout("\t");

				list($width, $height) = getimagesizefromstring($album->image);
				$this->stdout("{$width}x{$height}\t");
				if ($width > 999 && $height > 999) {
					list($album->image) = Lyrics2Albums::getCover(999, $album);
					$album->url = $album->name === $album->url ? null : $album->url;
					$album->save();
					list($width, $height) = getimagesizefromstring($album->image);
					$this->stdout($this->ansiFormat("{$width}x{$height}\n", Console::BOLD, Console::FG_GREEN));
					continue;
				}
				$this->stdout($this->ansiFormat("OK\n", Console::BOLD, Console::FG_GREEN));
			endforeach;
		endforeach;

		return Controller::EXIT_CODE_NORMAL;
	}
}
