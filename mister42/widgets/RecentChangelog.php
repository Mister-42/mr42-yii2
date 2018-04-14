<?php
namespace app\widgets;
use Yii;
use app\models\site\Changelog;
use yii\bootstrap\{Html, Widget};

class RecentChangelog extends Widget {
	public function run(): string {
		$logs = Changelog::find()
			->orderBy('time DESC')
			->limit(5)
			->all();
		return empty($logs) ? Html::tag('p', 'No changes to display.') : Html::tag('ul', self::renderLogs($logs), ['class' => 'list-unstyled']);
	}

	private function renderLogs(array $logs): string {
		foreach ($logs as $log) :
			$url = "https://github.com/Thoulah/mr.42/commit/{$log->id}";
			$logline = Html::a(Yii::$app->formatter->asNText($log->description), $url);
			$items[] = Html::tag('li', $logline);
		endforeach;
		return implode($items);
	}
}