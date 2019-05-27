<?php
namespace app\commands;
use Yii;
use app\models\Console;
use app\models\articles\Articles;
use yii\console\Controller;

/**
 * Handles all actions related to articles.
 */
class ArticlesController extends Controller {
	public $defaultAction = 'pdf';

	/**
	 * Builds all articles PDF files, unless already cached and up-to-date.
	 */
	public function actionPdf(): void {
		$query = Articles::find()->orderBy('created')->where(['pdf' => true]);
		$count = $query->count();
		Console::startProgress($x = 0, $count, 'Processing PDFs: ');
		foreach ($query->each() as $article) :
			Console::updateProgress(++$x, $count);
			if (Articles::buildPdf($article))
				continue;

			Console::write($article->id, [Console::FG_PURPLE]);
			Console::write(Yii::$app->formatter->asDate($article->updated, 'medium'), [Console::FG_GREEN], 2);
			Console::write($article->title, [Console::FG_GREEN], 8);
			Console::writeError("ERROR!", [Console::BOLD, Console::FG_RED, CONSOLE::BLINK]);
		endforeach;

		Console::endProgress(true);
	}
}
