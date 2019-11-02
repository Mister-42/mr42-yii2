<?php

namespace app\widgets;

use app\models\articles\Articles;
use Yii;
use yii\bootstrap4\Html;
use yii\bootstrap4\Widget;

class RecentArticles extends Widget
{
    public $limit = 5;

    public function run(): string
    {
        $articles = Articles::find()
            ->orderBy(['updated' => SORT_DESC])
            ->limit($this->limit)
            ->where(['active' => true])
            ->all();

        foreach ($articles as $article) {
            $link = Html::a($article->title, ['articles/article', 'id' => $article->id, 'title' => $article->url], ['class' => 'card-link stretched-link']);
            $items[] = Html::tag('li', $link, ['class' => 'list-group-item text-truncate']);
        }

        return (!isset($items))
            ? Html::tag('div', Yii::t('mr42', 'No Items to Display.'), ['class' => 'ml-2'])
            : Html::tag('ul', implode($items), ['class' => 'list-group list-group-flush']);
    }
}
