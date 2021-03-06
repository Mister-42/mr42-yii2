<?php

namespace mister42\widgets;

use mister42\models\articles\Tags;
use Yii;
use yii\bootstrap4\Html;

class TagCloud extends \yii\bootstrap4\Widget
{
    public function run(): string
    {
        foreach (Tags::findTagWeights() as $tag => $data) {
            $title = Yii::t('mr42', '{results, plural, =0{No articles} =1{1 article} other{# articles}} with tag "{tag}"', ['results' => $data['count'], 'tag' => $tag]);
            $items[] = Html::a($tag, ['articles/tag', 'tag' => $tag], ['class' => 'mx-2', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'style' => 'font-size:' . ($data['weight'] / 10) . 'rem', 'title' => $title]);
        }

        return (!isset($items) || empty($items))
            ? Html::tag('div', Yii::t('mr42', 'No Items to Display.'), ['class' => 'ml-2'])
            : Html::tag('div', implode(' ', $items), ['class' => 'text-center']);
    }
}
