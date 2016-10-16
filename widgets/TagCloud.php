<?php
namespace app\widgets;
use Yii;
use app\models\articles\{Articles, Tags};
use yii\bootstrap\{Html, Widget};

class TagCloud extends Widget
{
	public function run() {
		$limit = (isset(Yii::$app->params['tagCloud']) && is_int(Yii::$app->params['tagCloud'])) ? Yii::$app->params['tagCloud'] : 5;
		$tags = Tags::findTagWeights($limit);
		echo (empty($tags)) ? Html::tag('p', 'No tags to display.') : $this->renderTags($tags);
	}

	public function renderTags($tags) {
		foreach ($tags as $tag => $weight) :
			$query = Articles::find()->where(['like', 'tags', $tag])->count();
			$title = Yii::t('site', '{results, plural, =1{1 article} other{# articles}} with tag "{tag}"', ['results' => $query, 'tag' => $tag]);
			$link = Html::a(Html::encode($tag), ['articles/index', 'action' => 'tag', 'tag' => $tag], ['title' => $title, 'data-toggle' => 'tooltip', 'data-placement' => 'top']);
			$items[] = Html::tag('span', $link, ['style' => 'font-size:'.round(1.25 * $weight).'pt']);
		endforeach;

		return implode(' ', $items);
	}
}
