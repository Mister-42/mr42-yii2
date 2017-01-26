<?php
namespace app\widgets;
use Yii;
use yii\bootstrap\{Html, Widget};
use app\models\user\WeeklyArtist;

class WeeklyArtistChart extends Widget {
	public $profile;

	public function run() {
		$items = WeeklyArtist::find()
			->where(['userid' => 1])
			->orderBy('count DESC')
			->all();
		echo empty($items) ? Html::tag('p', 'No items to display.') : $this->renderFeed($items);
	}

	public function renderFeed($items) {
		foreach ($items as $item) :
			$count++;
			$line = '<div class="clearfix">';
				$line .= Html::tag('span', $count, ['class' => 'pull-left']);
				$line .= Html::tag('span', $item['artist'], ['class' => 'pull-left']);
				$line .= Html::tag('span', $item['count'], ['class' => 'pull-right text-right']);
			$line .= '</div>';

			$feed[] = $line;
		endforeach;
		return Html::tag('ul', implode($feed), ['class' => 'list-unstyled']);
	}
}
