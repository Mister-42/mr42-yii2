<?php

use app\models\Menu;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;

$this->title = Yii::$app->name;

echo Html::tag('div', Yii::t('mr42', 'This website is a hobby project. Some parts are created to make work or life a little bit easier, other parts are created for entertainment purposes only.'), ['class' => 'alert alert-info shadow']);

echo Html::beginTag('div', ['class' => 'site-index']);
echo Html::beginTag('div', ['class' => 'list-group']);
foreach ((new Menu())->getItemList() as $menu) {
    if (isset($menu['items'])) {
        foreach ($menu['items'] as $submenu) {
            if (isset($submenu['url']) && (!isset($submenu['visible']) || $submenu['visible'])) {
                $submenuItems[] = Html::beginTag('div', ['class' => 'list-group-item list-group-item-action px-4 py-0']);
                $submenuItems[] = Html::beginTag('span', ['class' => 'px-3 text-nowrap']);
                $submenuItems[] = isset($submenu['url'])
                    ? Html::a(Yii::$app->formatter->cleanInput($submenu['label'], false), $submenu['url'], [ArrayHelper::getValue($submenu, 'linkOptions', []), 'class' => 'stretched-link'])
                    : Yii::$app->formatter->cleanInput($submenu['label'], false);
                $submenuItems[] = Html::endTag('span');
                $submenuItems[] = Html::endTag('div');
            }
        }
    }

    if (!isset($menu['visible']) || $menu['visible']) {
        echo Html::beginTag('div', ['class' => ['list-group-item list-group-item-action p-0']]);
        echo Html::beginTag('span', ['class' => 'px-3']);
        echo (isset($menu['url']))
            ? Html::a(Yii::$app->formatter->cleanInput($menu['label'], false), $menu['url'], ['class' => 'stretched-link'])
            : Yii::$app->formatter->cleanInput($menu['label'], false);
        echo Html::endTag('span');
        if (!isset($menu['url'])) {
            echo implode($submenuItems);
        }
        echo Html::endTag('div');
    }
    unset($submenuItems);
}
echo Html::endTag('div');
echo Html::endTag('div');
