<?php
namespace app\widgets;
use Yii;
use yii\bootstrap4\{Html, Widget};
use app\models\feed\Feed as FeedModel;

class Feed extends Widget {
	public $name;
	public $limit = 10;
	public $tooltip = false;

	public function run(): string {
		$items = FeedModel::find()
			->where(['feed' => $this->name])
			->orderBy('time DESC')
			->limit($this->limit)
			->all();
		return empty($items) ? Html::tag('div', Yii::t('mr42', 'No items to display.'), ['class' => 'ml-2']) : self::renderFeed($items, $this->limit);
	}

	private function renderFeed(array $items, int $limit): string {
		$count = 0;
		foreach ($items as $item) :
			$feed[] = $this->tooltip && !empty($item['description'])
				? Html::tag('li', Html::a($item['title'], $item['url'], ['class' => 'card-link', 'title' => Html::tag('div', $item['title'], ['class' => 'font-weight-bold']).$item['description'], 'data-html' => 'true', 'data-toggle' => 'tooltip', 'data-placement' => 'left']), ['class' => 'list-group-item text-truncate'])
				: Html::tag('li', Html::a($item['title'], $item['url'], ['class' => 'card-link']), ['class' => 'list-group-item text-truncate']);
			if (++$count === $this->limit) :
				break;
			endif;
		endforeach;
		return Html::tag('ul', implode($feed), ['class' => 'list-group list-group-flush']);
	}
}
