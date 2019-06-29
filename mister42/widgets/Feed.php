<?php

namespace app\widgets;

use app\models\feed\Feed as FeedModel;
use Yii;
use yii\bootstrap4\Html;
use yii\bootstrap4\Widget;

class Feed extends Widget
{
    public $limit = 10;
    public $name;
    public $tooltip = false;

    public function run(): string
    {
        $items = FeedModel::find()
            ->where(['feed' => $this->name])
            ->orderBy(['time' => SORT_DESC])
            ->limit($this->limit)
            ->all();

        foreach ($items as $item) {
            $feed[] = ($this->tooltip && !empty($item['description']))
                ? Html::tag('li', Html::a($item['title'], $item['url'], ['class' => 'card-link', 'title' => Html::tag('div', $item['title'], ['class' => 'font-weight-bold']) . $item['description'], 'data-html' => 'true', 'data-toggle' => 'tooltip', 'data-placement' => 'left']), ['class' => 'list-group-item text-truncate'])
                : Html::tag('li', Html::a($item['title'], $item['url'], ['class' => 'card-link']), ['class' => 'list-group-item text-truncate']);
        }

        return (!isset($feed))
            ? Html::tag('div', Yii::t('mr42', 'No Items to Display.'), ['class' => 'ml-2'])
            : Html::tag('ul', implode($feed), ['class' => 'list-group list-group-flush']);
    }
}
