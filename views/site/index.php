<?php
use app\models\Menu;
use yii\bootstrap\{Carousel, Html};
use yii\helpers\FileHelper;

$this->title = Yii::$app->name;
$img = FileHelper::findFiles(Yii::$app->assetManager->getBundle('app\assets\ImagesAsset')->basePath . '/site/index/', ['only'=>['*.jpg', '*.png'], 'recursive' => false]);
sort($img);
foreach($img as $file)
	$images[] = Html::img(Yii::$app->assetManager->getBundle('app\assets\ImagesAsset')->baseUrl.'/site/index/' . basename($file));

echo Html::tag('h2', 'Welcome to '.Yii::$app->name);
echo Html::tag('p', 'This website is merely a hobby project. Some parts are created to make work or life a little bit easier, other parts are created for entertainment purposes only.');
echo Carousel::widget(['controls' => false, 'items' => $images, 'showIndicators' => false]);
echo Html::tag('p', 'Below is an overview of the items in the menu for a quick overview.');

echo '<ul>';
foreach(Menu::getMenu() as $menu) :
	echo isset($menu['url'])
		? Html::tag('li', Html::a(Yii::$app->formatter->cleanInput($menu['label'], false), $menu['url']))
		: Html::tag('li', Yii::$app->formatter->cleanInput($menu['label'], false));
	if ($menu['items']) {
		foreach($menu['items'] as $submenu) :
			if (isset($submenu['url']) && (!isset($submenu['visible']) || $submenu['visible']))
				$submenuItems[] = isset($submenu['url'])
					? Html::a(Yii::$app->formatter->cleanInput($submenu['label'], false), $submenu['url'])
					: Yii::$app->formatter->cleanInput($submenu['label'], false);
		endforeach;
		echo Html::ul($submenuItems, ['encode' => false]);
		unset($submenuItems);
	}
endforeach;
echo '</ul>';
