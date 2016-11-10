<?php
namespace app\widgets;
use Yii;
use yii\bootstrap\{Html, Widget};
use app\models\feed\Feed as FeedModel;

class Feed extends Widget {
	public $name;
	public $limit;

	public function run() {
		$limit = is_int(Yii::$app->params['feedItemCount']) ? Yii::$app->params['feedItemCount'] : 10;
		$limit = $this->limit ?? $limit;
		$items = FeedModel::find()
			->where(['feed' => $this->name])
			->orderBy('time DESC')
			->limit($limit)
			->all();
		echo empty($items) ? Html::tag('p', 'No items to display.') : $this->renderFeed($items, $limit);
	}

	public function renderFeed($items, $limit) {
		$count = 1;
		foreach ($items as $item) :
			$feed[] = Html::tag('li', Html::a(Html::encode($item['title']), $item['url'], ['title' => $item['description'], 'data-toggle' => 'tooltip', 'data-placement' => 'top']));
			if ($count++ === $limit)
				break;
		endforeach;
		return Html::tag('ul', implode($feed), ['class' => 'list-unstyled']);
	}
}
