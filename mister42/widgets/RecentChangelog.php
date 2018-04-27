<?php
namespace app\widgets;
use Yii;
use app\models\site\Changelog;
use yii\bootstrap4\{Html, Widget};

class RecentChangelog extends Widget {
	public function run(): string {
		$logs = Changelog::find()
			->orderBy('time DESC')
			->limit(5)
			->all();
		return empty($logs) ? Html::tag('p', 'No changes to display.') : self::renderLogs($logs);
	}

	private function renderLogs(array $logs): string {
		foreach ($logs as $log) :
			$url = "https://github.com/Thoulah/mr.42/commit/{$log->id}";
			$logline = Html::a(Yii::$app->formatter->asNText($log->description), $url, ['class' => 'card-link']);
			$items[] = Html::tag('li', $logline, ['class' => 'list-group-item text-truncate']);
		endforeach;
		return Html::tag('ul', implode($items), ['class' => 'list-group list-group-flush']);
	}
}
