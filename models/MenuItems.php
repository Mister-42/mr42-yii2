<?php
namespace app\models;
use Yii;

class MenuItems
{
	public static function menuArray() {
		$isGuest = (Yii::$app->controller->action->id == 'sitemapxml') ? true : Yii::$app->user->isGuest;
		$isAdmin = (!$isGuest && Yii::$app->user->identity->isAdmin) ? true : false;
		$username = $isGuest ? '' : Yii::$app->user->identity->username;

		$menuItems = [
			['label' => 'About', 'url' => ['/site/about'], 'visible' => 0],
			['label' =>  'Articles', 'url' => ['/post/index'], 'visible' => 1],
			['label' => 'Calculator', 'url' => null,
				'items' => [
					['label' => 'Date (add/substract)', 'url' => ['/calculator/date']],
					['label' => 'Date to Date (duration)', 'url' => ['/calculator/duration']],
					['label' => 'Microsoft® Office 365® End Date', 'url' => ['/calculator/office365']],
				],
			],
			['label' => 'Tools', 'url' => null,
				'items' => [
					['label' => 'Code Playground', 'url' => ['/tech/playground'], 'visible' => $isAdmin],
					['label' => 'Browser Headers', 'url' => ['/tools/headers']],
					['label' => 'Favicon Converter', 'url' => ['/tools/favicon']],
					['label' => 'Password Generator', 'url' => ['/tools/password']],
					['label' => 'WPA PSK Calculator', 'url' => ['/tools/wpapsk']],
				],
			],
			['label' => 'Lyrics', 'url' => ['/lyrics/index'], 'visible' => 1],
			$isGuest ?
				['label' => 'Login', 'url' => ['/user/security/login'], 'visible' => 1]
			:
				['label' => $username, 'url' => null,
					'items' => [
						['label' => 'Create Article', 'url' => ['/post/create'], 'visible' => $isAdmin],
						['label' => 'Manage Users', 'url' => ['/user/admin/index'], 'visible' => $isAdmin],
						$isAdmin ? '<li class="divider"></li>' : '',
						['label' => 'Edit Profile', 'url' => ['/user/settings/profile']],
						['label' => 'Account Settings', 'url' => ['/user/settings/account']],
						['label' => 'Social Networks', 'url' => ['/user/settings/networks']],
						'<li class="divider"></li>',
						['label' => 'Logout', 'url' => ['/user/security/logout'], 'linkOptions' => ['data-method' => 'post']],
					],
				]
			,
		];

		if (Yii::$app->controller->action->id == 'sitemapxml') {
			$menuItems[] = ['label' => 'Create Account', 'url' => ['/user/registration/register']];
			$menuItems[] = ['label' => 'Contact', 'url' => ['/site/changelog']];
			$menuItems[] = ['label' => 'Contact', 'url' => ['/site/contact']];
			$menuItems[] = ['label' => 'Credits', 'url' => ['/site/credits']];
		}
		return $menuItems;
	}

	public static function urlList() {
		$pages = [];
		$menu = self::menuArray();

		foreach ($menu as $item) {
			if (isset($item['visible'])) continue;

			if (isset($item['url'])) {
				$pages[] = $item['url'][0];
			}

			if (isset($item['items'])) {
				foreach ($item['items'] as $subitem) {
					if (isset($subitem['visible'])) continue;

					if (isset($subitem['url'])) {
						$pages[] = $subitem['url'][0];
					}
				}
			}
		}

		return $pages;
	}
}
