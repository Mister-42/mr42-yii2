<?php
use app\models\MenuItems;
use yii\bootstrap\Html;

$this->title = Yii::$app->name;

echo Html::tag('h2', 'Welcome to '.Yii::$app->name);
echo Html::tag('p', 'This website is merely a hobby project. Some parts are created to make work or life a little bit easier, other parts are created for entertainment purposes only.');
echo Html::tag('p', 'Below is an overview of the items in the menu for a quick overview.');

echo '<ul>';
foreach(MenuItems::menuArray() as $menu) :
	echo isset($menu['url'])
		? Html::tag('li', Html::a(Yii::$app->formatter->cleanInput($menu['label'], false), $menu['url']))
		: Html::tag('li', Yii::$app->formatter->cleanInput($menu['label'], false));
	if ($menu['items']) {
		foreach($menu['items'] as $submenu) :
			if (isset($submenu['url']) && (!isset($submenu['visible']) || $submenu['visible']))
				$submenuitems[] = isset($submenu['url'])
					? Html::a(Yii::$app->formatter->cleanInput($submenu['label'], false), $submenu['url'])
					: Yii::$app->formatter->cleanInput($submenu['label'], false);
		endforeach;
		echo Html::ul($submenuitems, ['encode' => false]);
		unset($submenuitems);
	}
endforeach;
echo '</ul>';
