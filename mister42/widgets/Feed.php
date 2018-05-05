<?php
namespace app\widgets;
use Yii;
use yii\bootstrap4\{Html, Widget};
use app\models\feed\Feed as FeedModel;

class Feed extends Widget {
	public $name;
	public $limit;

	public function run(): string {
		$limit = is_int(Yii::$app->params['feedItemCount']) ? Yii::$app->params['feedItemCount'] : 10;
		$limit = $this->limit ?? $limit;
		$items = FeedModel::find()
			->where(['feed' => $this->name])
			->orderBy('time DESC')
			->limit($limit)
			->all();
		return empty($items) ? Html::tag('p', 'No items to display.') : self::renderFeed($items, $limit);
	}

	private function renderFeed(array $items, int $limit): string {
		foreach ($items as $item) :
			$feed[] = $item['title'] === $item['description'] || $item['description'] === null
				? Html::tag('li', Html::a($item['title'], $item['url'], ['class' => 'card-link']), ['class' => 'list-group-item text-truncate'])
				: Html::tag('li', Html::a($item['title'], $item['url'], ['class' => 'card-link', 'title' => Html::tag('div', $item['title'], ['class' => 'font-weight-bold']) . $item['description'], 'data-html' => 'true', 'data-toggle' => 'tooltip', 'data-placement' => 'left']), ['class' => 'list-group-item text-truncate']);
			if (++$count === $limit)
				break;
		endforeach;
		return Html::tag('ul', implode($feed), ['class' => 'list-group list-group-flush']);
	}
}
