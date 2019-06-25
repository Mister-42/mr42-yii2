<?php

use app\models\feed\Sitemap;
use app\models\Menu;
use yii\base\View;

$doc = Sitemap::beginDoc();

Sitemap::lineItem($doc, ['site/index'], ['age' => filemtime(View::findViewFile('@app/views/site/index')), 'locale' => true, 'priority' => 1]);

foreach ((new Menu())->getUrlList() as $page) {
    Sitemap::lineItem($doc, [$page], ['age' => filemtime(View::findViewFile('@app/views' . $page)), 'locale' => true]);
}

echo Sitemap::endDoc($doc);
