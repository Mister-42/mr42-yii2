<?php
namespace app\widgets;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use app\models\Feed as FeedModel;

class Feed extends Widget
{
	public $name;
	public $limit;

	public function run()
	{
		$limit = (isset(Yii::$app->params['rssItemCount']) && is_int(Yii::$app->params['rssItemCount'])) ? Yii::$app->params['rssItemCount'] : 10;
		$limit = (isset($this->limit)) ? $this->limit : $limit;
		$items = FeedModel::find()
			->where(['feed' => $this->name])
			->orderBy('time DESC')
			->limit($limit)
			->all();
		echo (empty($items)) ? Html::tag('p', 'No items to display.') : $this->renderFeed($items, $limit);
	}

	public function renderFeed($items, $limit)
	{
		$count = 0;
		foreach ($items as $item) {
			$count++;
			$feed[] = Html::tag('li', Html::a(Html::encode($item['title']), $item['url'], ['title' => $item['description'], 'data-toggle' => 'tooltip', 'data-placement' => 'top']));

			if ($count === $limit)
				break;
		}

		return Html::tag('ul', implode('', $feed), ['class' => 'list-unstyled']);
	}
}
