<?php
namespace app\widgets;
use Yii;
use app\models\articles\Tags;
use yii\bootstrap4\{Html, Widget};

class TagCloud extends Widget {
	public function run(): string {
		return empty($tags = Tags::findTagWeights()) ? Html::tag('div', Yii::t('mr42', 'No items to display.'), ['class' => 'ml-2']) : self::renderTags($tags);
	}

	private function renderTags(array $tags): string {
		foreach ($tags as $tag => $data) :
			$title = Yii::t('mr42', '{results, plural, =0{No articles} =1{1 article} other{# articles}} with tag "{tag}"', ['results' => $data['count'], 'tag' => $tag]);
			$items[] = Html::a($tag, ['articles/index', 'action' => 'tag', 'q' => $tag], ['class' => 'card-link', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'style' => 'font-size:'.($data['weight'] / 10).'rem', 'title' => $title]);
		endforeach;
		return Html::tag('div', implode(' ', $items), ['class' => 'text-center']);
	}
}
