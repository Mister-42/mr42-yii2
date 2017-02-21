<?php
namespace app\models;
use Yii;
use app\models\articles\Comments;
use yii\bootstrap\Html;

class MenuItems {
	public function menuArray() {
		$isGuest = (Yii::$app->controller->action->id === 'sitemap') ? true : Yii::$app->user->isGuest;
		$isAdmin = !$isGuest && Yii::$app->user->identity->isAdmin;
		$unread = $isAdmin ? Comments::find()->where(['active' => Comments::STATUS_INACTIVE])->count() : 0;
		$unreadBadge = $unread > 0 ? Html::tag('span', $unread, ['class' => 'badge']) : '';

		$menuItems = [
			['label' => Html::icon('th-list').'Articles', 'url' => ['/articles/index'], 'visible' => 1],
			['label' => Html::icon('dashboard').'Calculator', 'url' => null,
				'items' => [
					['label' => 'Date (add/subtract)', 'url' => ['/calculator/date']],
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
					['label' => 'HTML to Markdown Converter', 'url' => ['/tools/html-to-markdown']],
					['label' => 'Password Generator', 'url' => ['/tools/password']],
					['label' => 'Phonetic Alphabet Translator', 'url' => ['/tools/phonetic-alphabet']],
				],
			],
			['label' => Html::icon('cd').'Lyrics', 'url' => ['/lyrics/index'], 'visible' => 1],
			$isGuest
				?	['label' => Html::icon('log-in').'Login', 'url' => ['/user/security/login'], 'visible' => 1]
				:	['label' => Html::icon('user').Yii::$app->user->identity->username.' '.$unreadBadge, 'url' => null,
						'items' => [
							['label' => 'Create Article', 'url' => ['/articles/create'], 'visible' => $isAdmin],
							['label' => 'Manage Users', 'url' => ['/user/admin/index'], 'visible' => $isAdmin],
							['label' => 'PHP version', 'url' => ['/site/php'], 'visible' => $isAdmin],
							$isAdmin ? Html::tag('li', null, ['class' => 'divider']) : '',
							['label' => 'View Profile', 'url' => ['/user/profile/show', 'username' => Yii::$app->user->identity->username]],
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

		if (Yii::$app->controller->action->id === 'sitemap') {
			$menuItems[] = ['label' => 'Create Account', 'url' => ['/user/registration/register']];
			$menuItems[] = ['label' => 'Contact', 'url' => ['/site/changelog']];
			$menuItems[] = ['label' => 'Contact', 'url' => ['/site/contact']];
		}
		return $menuItems;
	}

	public function urlList() {
		foreach (self::menuArray() as $item) :
			if (isset($item['visible']))
				continue;

			if (isset($item['url']))
				$pages[] = $item['url'][0];

			if (isset($item['items'])) {
				foreach ($item['items'] as $subitem) :
					if (isset($subitem['visible']))
						continue;

					if (isset($subitem['url']))
						$pages[] = $subitem['url'][0];
				endforeach;
			}
		endforeach;
		return $pages;
	}
}
