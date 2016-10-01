<?php
use app\models\General;
use yii\bootstrap\Html;

$this->title = Yii::$app->name;

echo Html::tag('h2', 'Welcome to '.Yii::$app->name);
echo Html::tag('p', 'This website is merely a hobby project. Some parts are created to make work or life a little bit easier, other parts are created for entertainment purposes only.');
echo Html::tag('p', 'Below is an overview of the items in the menu for a quick overview.');

echo '<ul>';
foreach($pages as $menu) :
	if (!isset($menu['visible']) || $menu['visible']) {
		echo (isset($menu['url'])) ? Html::tag('li', Html::a(General::cleanInput($menu['label'], false), $menu['url'])) : Html::tag('li', General::cleanInput($menu['label'], false));
		if ($menu['items']) {
			echo '<ul>';
			foreach($menu['items'] as $submenu) :
				if (!isset($submenu['visible']) && isset($submenu['label'])) {
					echo (isset($submenu['url'])) ? Html::tag('li', Html::a(General::cleanInput($submenu['label'], false), $submenu['url'])) : Html::tag('li', General::cleanInput($submenu['label'], false));
				}
			endforeach;
			echo '</ul>';
		}
	}
endforeach;
echo '</ul>';
