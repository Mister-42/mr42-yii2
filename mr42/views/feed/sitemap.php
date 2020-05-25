<?php

use mister42\models\Menu;
use mr42\models\Sitemap;
use yii\base\View;

$doc = Sitemap::beginDoc();

Sitemap::lineItem($doc, ['site/index'], ['age' => filemtime(View::findViewFile('@mister42/views/site/index')), 'locale' => true, 'priority' => 1]);

foreach ((new Menu())->getUrlList() as $page) {
    Sitemap::lineItem($doc, [$page], ['age' => filemtime(View::findViewFile('@mister42/views' . $page)), 'locale' => true]);
}

echo Sitemap::endDoc($doc);
