<?php
namespace app\commands;
use Yii;
use app\models\articles\Articles;
use app\models\lyrics\{Lyrics1Artists, Lyrics2Albums};
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Handles all actions related to articles.
 */
class ArticlesController extends Controller {
	const TABSIZE = 8;
	public $defaultAction = 'pdf';

	/**
	 * Builds all articles PDF files, unless already cached and up-to-date.
	*/
	public function actionPdf() {
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
