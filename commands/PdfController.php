<?php
namespace app\commands;
use Yii;
use app\models\articles\Articles;
use app\models\lyrics\{Lyrics1Artists, Lyrics2Albums, Lyrics3Tracks};
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Builds PDF files for cache, unless already cached and up-to-date.
 */
class PdfController extends Controller {
	const TABSIZE = 8;
	public $defaultAction = 'all';

	/**
	 * Builds all PDF files.
	*/
	public function actionAll() {
		self::actionAlbums();
		self::actionArticles();
	}

	/**
	 * Builds all albums PDF files.
	*/
	public function actionAlbums() {
		foreach(Lyrics1Artists::albumsList() as $artist) :
			$artistName = $this->ansiFormat($artist->name, Console::FG_PURPLE);
			foreach($artist->albums as $album) :
				if (!$album->active)
					continue;
				$albumYear = $this->ansiFormat($album->year, Console::FG_GREEN);
				$albumName = $this->ansiFormat($album->name, Console::FG_GREEN);
				$this->stdout("$artistName");
				for($x=0; $x<(3 - intdiv(mb_strlen($artist->name), Self::TABSIZE)); $x++)
					$this->stdout("\t");
				$this->stdout("$albumYear\t\t$albumName");
				for($x=0; $x<(8 - intdiv(mb_strlen($album->name), Self::TABSIZE)); $x++)
					$this->stdout("\t");

				$fileName = Lyrics2Albums::buildPdf($album, $this->renderPartial('@app/views/lyrics/albumPdf', ['tracks' => $album->tracks]));
				if (!$fileName) {
					$this->stdout($this->ansiFormat("ERROR!\n", Console::BOLD, Console::FG_RED));
					continue;
				}

				$this->stdout(Yii::$app->formatter->asShortSize(filesize($fileName), 2)."\t");
				$this->stdout($this->ansiFormat("OK\n", Console::BOLD, Console::FG_GREEN));
			endforeach;
		endforeach;

		return Controller::EXIT_CODE_NORMAL;
	}

	/**
	 * Builds all articles PDF files.
	*/
	public function actionArticles() {
		$articles = Articles::find()
			->orderBy('created')
			->all();

		foreach($articles as $article) :
			$id = $this->ansiFormat($article->id, Console::FG_PURPLE);
			$updated = $this->ansiFormat(Yii::$app->formatter->asDate($article->updated, 'medium'), Console::FG_GREEN);
			$title = $this->ansiFormat($article->title, Console::FG_GREEN);
			$this->stdout("$id\t\t\t$updated\t$title");
			for($x=0; $x<(8 - intdiv(mb_strlen($article->title), Self::TABSIZE)); $x++)
				$this->stdout("\t");

			$html = $this->renderPartial('@app/views/articles/pdf', ['model' => $article]);
			$fileName = Articles::buildPdf($article, $html);

			if (!$fileName) {
				$this->stdout($this->ansiFormat("ERROR!\n", Console::BOLD, Console::FG_RED));
				continue;
			}

			$this->stdout(Yii::$app->formatter->asShortSize(filesize($fileName), 2)."\t");
			$this->stdout($this->ansiFormat("OK\n", Console::BOLD, Console::FG_GREEN));
		endforeach;

		return Controller::EXIT_CODE_NORMAL;
	}
}
