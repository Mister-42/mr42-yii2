<?php

use mister42\models\Menu;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;

$this->title = Yii::$app->name;

echo Html::tag('div', Yii::t('mr42', 'This website is a hobby project. Some parts are created to make work or life a little bit easier, other parts are created for entertainment purposes only.'), ['class' => 'alert alert-info shadow']);

echo Html::beginTag('div', ['class' => 'site-index']);
    echo Html::beginTag('div', ['class' => 'list-group']);
        foreach ((new Menu())->getItemList() as $item) {
            if (!$item['visible'] || $item['visible']) {
                echo Html::beginTag('div', ['class' => ['list-group-item list-group-item-action p-0']]);
                echo ($item['url'])
                    ? Html::a(strip_tags($item['label']), $item['url'], ['class' => 'stretched-link'])
                    : strip_tags($item['label']);

                if ($item['items']) {
                    foreach ($item['items'] as $subItem) {
                        if (isset($subItem['url']) && (!isset($subItem['visible']) || $subItem['visible'])) {
                            echo Html::beginTag('div', ['class' => 'list-group-item list-group-item-action py-0']);
                            echo Html::a($subItem['label'], $subItem['url'], [ArrayHelper::getValue($subItem, 'linkOptions'), 'class' => 'stretched-link']);
                            echo Html::endTag('div');
                        }
                    }
                }
                echo Html::endTag('div');
            }
        }
    echo Html::endTag('div');
echo Html::endTag('div');
