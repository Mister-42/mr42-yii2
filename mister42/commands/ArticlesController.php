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
	public function actionPdf(): int {
		foreach (Articles::find()->orderBy('created')->where(['pdf' => true])->all() as $article) :
			Console::write($article->id, [Console::FG_PURPLE]);
			Console::write(Yii::$app->formatter->asDate($article->updated, 'medium'), [Console::FG_GREEN], 2);
			Console::write($article->title, [Console::FG_GREEN], 8);

			$fileName = Articles::buildPdf($article);
			if (!$fileName) :
				Console::writeError("ERROR!", [Console::BOLD, Console::FG_RED]);
				return self::EXIT_CODE_ERROR;
			endif;

			Console::write(Yii::$app->formatter->asShortSize(filesize($fileName), 2), [Console::FG_GREEN], 2);
			Console::write('OK', [Console::BOLD, Console::FG_GREEN]);
			Console::newLine();
		endforeach;

		return self::EXIT_CODE_NORMAL;
	}
}
