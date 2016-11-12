<?php
namespace app\widgets;
use Yii;
use app\models\articles\Tags;
use yii\bootstrap\{Html, Widget};

class TagCloud extends Widget {
	public function run() {
		echo empty($tags = Tags::findTagWeights()) ? Html::tag('p', 'No tags to display.') : $this->renderTags($tags);
	}

	public function renderTags($tags) {
		foreach ($tags as $tag => $data) :
			$title = Yii::t('site', '{results, plural, =1{1 article} other{# articles}} with tag "{tag}"', ['results' => $data['count'], 'tag' => $tag]);
			$link = Html::a(Html::encode($tag), ['articles/index', 'action' => 'tag', 'tag' => $tag], ['title' => $title, 'data-toggle' => 'tooltip', 'data-placement' => 'top']);
			$items[] = Html::tag('span', $link, ['style' => 'font-size:'.round(1.25 * $data['weight']).'pt']);
		endforeach;
		return implode(' ', $items);
	}
}
