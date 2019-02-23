<?php
use app\models\Menu;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;

$this->title = Yii::$app->name;

echo Html::tag('div', Yii::t('mr42', 'This website is merely a hobby project. Some parts are created to make work or life a little bit easier, other parts are created for entertainment purposes only.'), ['class' => 'alert alert-info']);

echo Html::beginTag('ul', ['class' => 'list-unstyled']);
$menuList = new Menu();
foreach ($menuList->getItemList() as $menu) :
	if (isset($menu['items'])) :
		foreach ($menu['items'] as $submenu) :
			if (isset($submenu['url']) && (!isset($submenu['visible']) || $submenu['visible']))
				$submenuItems[] = isset($submenu['url'])
					? Html::a(Yii::$app->formatter->cleanInput($submenu['label'], false), $submenu['url'], ArrayHelper::getValue($submenu, 'linkOptions', []))
					: Yii::$app->formatter->cleanInput($submenu['label'], false);
		endforeach;
	endif;
	echo (isset($menu['url']))
		? (!isset($menu['visible']) || $menu['visible'])
			? Html::tag('li', Html::a(Yii::$app->formatter->cleanInput($menu['label'], false), $menu['url'], ['class' => 'font-weight-bold']))
			: ''
		: Html::tag('li', Yii::$app->formatter->cleanInput($menu['label'], false), ['class' => 'font-weight-bold'])
			. Html::tag('li', Html::ul($submenuItems, ['encode' => false]));
	unset($submenuItems);
endforeach;
echo Html::endTag('ul');
