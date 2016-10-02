<?php
namespace app\models;
use Yii;
use yii\bootstrap\Html;

class MenuItems
{
	public static function menuArray() {
		$isGuest = (Yii::$app->controller->action->id == 'sitemapxml') ? true : Yii::$app->user->isGuest;
		$isAdmin = !$isGuest && Yii::$app->user->identity->isAdmin;
		$username = $isGuest ? '' : Yii::$app->user->identity->username;

		$menuItems = [
			['label' => Html::icon('th-list').'Articles', 'url' => ['/post/index'], 'visible' => 1],
			['label' => Html::icon('dashboard').'Calculator', 'url' => null,
				'items' => [
					['label' => 'Date (add/substract)', 'url' => ['/calculator/date']],
					['label' => 'Date to Date (duration)', 'url' => ['/calculator/duration']],
					['label' => 'Microsoft® Office 365® End Date', 'url' => ['/calculator/office365']],
					['label' => 'Wifi Protected Access Pre-Shared Key', 'url' => ['/calculator/wpapsk']],
				],
			],
			['label' => Html::icon('wrench').' Tools', 'url' => null,
				'items' => [
					['label' => 'Code Playground', 'url' => ['/site/playground'], 'visible' => $isAdmin],
					['label' => 'Browser Headers', 'url' => ['/tools/headers']],
					['label' => 'Favicon Converter', 'url' => ['/tools/favicon']],
					['label' => 'Password Generator', 'url' => ['/tools/password']],
					['label' => 'Phonetic Alphabet Translator', 'url' => ['/tools/phonetic-alphabet']],
				],
			],
			['label' => Html::icon('cd').'Lyrics', 'url' => ['/lyrics/index'], 'visible' => 1],
			$isGuest ?
				['label' => Html::icon('log-in').'Login', 'url' => ['/user/security/login'], 'visible' => 1]
			:
				['label' => Html::icon('user').$username, 'url' => null,
					'items' => [
						['label' => 'Create Article', 'url' => ['/post/create'], 'visible' => $isAdmin],
						['label' => 'Manage Users', 'url' => ['/user/admin/index'], 'visible' => $isAdmin],
						$isAdmin ? Html::tag('li', null, ['class' => 'divider']) : '',
						['label' => 'View Profile', 'url' => ['/user/profile/show', 'username' => $username]],
						Html::tag('li', null, ['class' => 'divider']),
						['label' => 'Edit Profile', 'url' => ['/user/settings/profile']],
						['label' => 'Account Settings', 'url' => ['/user/settings/account']],
						['label' => 'Social Networks', 'url' => ['/user/settings/networks']],
						Html::tag('li', null, ['class' => 'divider']),
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
		foreach (self::menuArray() as $item) :
			if (isset($item['visible'])) continue;

			if (isset($item['url'])) {
				$pages[] = $item['url'][0];
			}

			if (isset($item['items'])) {
				foreach ($item['items'] as $subitem) :
					if (isset($subitem['visible'])) continue;

					if (isset($subitem['url'])) {
						$pages[] = $subitem['url'][0];
					}
				endforeach;
			}
		endforeach;

		return $pages;
	}
}
