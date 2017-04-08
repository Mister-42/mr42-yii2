<?php
namespace app\widgets;
use Yii;
use dektrium\user\models\User;
use app\models\user\WeeklyArtist;
use yii\bootstrap\{Html, Widget};

class WeeklyArtistChart extends Widget {
	public $profile;

	public function run(): string {
		$user = User::find()->where(['username' => $this->profile])->one();
		$items = WeeklyArtist::find()->where(['userid' => $user->id])->orderBy('count DESC')->all();
		return empty($items) ? Html::tag('p', 'No items to display.') : $this->renderFeed($items);
	}

	public function renderFeed(array $items): string {
		foreach ($items as $item)
			$feed[] = Html::tag('div',
				Html::tag('span', ++$count, ['class' => 'pull-left']) .
				Html::tag('span', $item['artist'], ['class' => 'pull-left']) .
				Html::tag('span', $item['count'], ['class' => 'pull-right text-right'])
			, ['class' => 'clearfix']);
		return implode($feed);
	}
}
