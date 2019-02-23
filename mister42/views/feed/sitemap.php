<?php
use app\models\Menu;
use app\models\feed\Sitemap;
use yii\base\View;

$doc = Sitemap::beginDoc();

Sitemap::lineItem($doc, ['site/index'], ['age' => filemtime(View::findViewFile('@app/views/site/index')), 'locale' => true, 'priority' => 1]);

$menuList = new Menu();
foreach ($menuList->getUrlList() as $page)
	Sitemap::lineItem($doc, [$page], ['age' => filemtime(View::findViewFile('@app/views'.$page)), 'locale' => true]);

echo Sitemap::endDoc($doc);
