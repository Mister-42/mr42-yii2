<?php
namespace app\widgets;
use Yii;
use app\models\post\Tags;
use yii\base\Widget;
use yii\bootstrap\Html;

class TagCloud extends Widget
{
	public function run()
	{
		$limit = (isset(Yii::$app->params['tagCloud']) && is_int(Yii::$app->params['tagCloud'])) ? Yii::$app->params['tagCloud'] : 5;
		$tags = Tags::findTagWeights($limit);
		echo (empty($tags)) ? Html::tag('p', 'No tags to display.') : $this->renderTags($tags);
	}

	public function renderTags($tags)
	{
		foreach ($tags as $tag => $weight) {
			$link = Html::a(Html::encode($tag), ['post/index', 'action' => 'tag', 'tag' => $tag]);
			$items[] = Html::tag('span', $link, ['style' => 'font-size:'.round(1.25 * $weight).'pt']);
		}

		return implode(' ', $items);
	}
}
