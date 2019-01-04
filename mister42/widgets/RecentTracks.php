<?php
namespace app\widgets;
use Yii;
use yii\bootstrap4\{Html, Widget};

class RecentTracks extends Widget {
	public $tracks;

	public function run(): string {
		return empty($this->tracks) ? Html::tag('div', 'No Items to Display.', ['class' => 'ml-2']) : $this->renderFeed($this->tracks);
	}

	private function renderFeed(array $items): string {
		foreach ($items as $item) :
			$feed[] = Html::tag('li',
				Html::tag('span', $item['artist'].(($item['time'] === 0) ? Yii::$app->icon->show('volume-up', ['class' => 'ml-1', 'title' => Yii::t('mr42', 'Currently Playing')]) : ''), ['class' => 'float-left mw-100 text-truncate']).
				Html::tag('span', $item['track'], ['class' => 'float-right text-right mw-100 text-truncate'])
			, ['class' => 'list-group-item w-100']);
		endforeach;

		$feed[] = Html::tag('li',
				Html::tag('span', Yii::t('mr42', 'Total Tracks Played:'), ['class' => 'font-weight-bold float-left']).
				Html::tag('span', Yii::$app->formatter->asInteger($items[0]['count']), ['class' => 'font-weight-bold float-right'])
			, ['class' => 'list-group-item']);

		return Html::tag('ul', implode($feed), ['class' => 'list-group list-group-flush']);
	}
}
