<?php

namespace mister42\widgets;

use mister42\models\articles\ArticlesComments;
use Yii;
use yii\bootstrap4\Html;
use yii\bootstrap4\Widget;

class RecentComments extends Widget
{
    public $limit = 5;

    public function run(): string
    {
        $comments = ArticlesComments::find()
            ->orderBy(['created' => SORT_DESC])
            ->with('article')
            ->where(['parent_comment' => null])
            ->limit($this->limit)
            ->all();

        foreach ($comments as $comment) {
            $draft = ($comment->active === 1) ? '' : Html::tag('sup', Yii::t('mr42', 'Draft'), ['class' => 'badge badge-info ml-1']);
            $link = Html::a($comment->title . $draft, ['articles/article', 'id' => $comment->article->id, 'title' => $comment->article->url, '#' => $comment->id], ['class' => 'card-link stretched-link']);
            $items[] = Html::tag('li', $link, ['class' => 'list-group-item text-truncate']);
        }

        return (!isset($items) || empty($items))
            ? Html::tag('div', Yii::t('mr42', 'No Items to Display.'), ['class' => 'ml-2'])
            : Html::tag('ul', implode($items), ['class' => 'list-group list-group-flush']);
    }
}
