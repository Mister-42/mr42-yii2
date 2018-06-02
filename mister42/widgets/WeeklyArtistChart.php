<?php
namespace app\widgets;
use Yii;
use app\models\user\WeeklyArtist;
use Da\User\Model\User;
use yii\bootstrap4\{Html, Widget};

class WeeklyArtistChart extends Widget {
	public $profile;

	public function run(): string {
		$user = User::find()->where(['username' => $this->profile])->one();
		$items = WeeklyArtist::find()->where(['userid' => $user->id])->orderBy('rank')->all();
		return empty($items) ? Html::tag('div', 'No Items to Display.', ['class' => 'ml-2']) : self::renderFeed($items);
	}

	private function renderFeed(array $items): string {
		foreach ($items as $item) :
			$feed[] = Html::tag('div',
				Html::tag('span', $item['rank'], ['class' => 'float-left']).
				Html::tag('span', $item['artist'], ['class' => 'float-left']).
				Html::tag('span', $item['count'], ['class' => 'float-right text-right'])
			, ['class' => 'clearfix']);
		endforeach;
		return implode($feed);
	}
}
