<?php
namespace app\models;
use Yii;
use yii\helpers\ArrayHelper;

class Menu {
	public function getItemList(): array {
		$menuItems = require(Yii::getAlias('@app/data/menu.php'));

		if (Yii::$app->controller->action->id === 'sitemap') :
			$menuItems[] = ['label' => 'Create Account', 'url' => ['/user/registration/register']];
			$menuItems[] = ['label' => 'Contact', 'url' => ['/site/contact']];
		endif;
		return $menuItems;
	}

	public function getUrlList($items = null): array {
		foreach ($items ?? self::getItemList() as $item) :
			if (!is_array($item) || ArrayHelper::keyExists('visible', $item)) :
				continue;
			endif;

			if (isset($item['url'])) :
				$pages[] = ArrayHelper::getValue($item, 'url.0');
			endif;

			if (isset($item['items'])) :
				$pages[] = self::getUrlList($item['items']);
			endif;
		endforeach;
		array_walk_recursive($pages, function($val) use (&$return) { $return[] = $val; });
		return $return;
	}
}
