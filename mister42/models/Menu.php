<?php
namespace app\models;
use Yii;
use app\models\articles\ArticlesComments;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;

class Menu extends \yii\base\Model {
	private $menuItems;

	public function init(): void {
		$this->menuItems = $this->getData();
		if (Yii::$app->controller->action->id === 'sitemap') :
			$this->menuItems[] = ['label' => null, 'url' => ['/user/registration/register']];
			$this->menuItems[] = ['label' => null, 'url' => ['/site/privacy']];
		endif;
	}

	public function getItemList(): array {
		foreach ($this->menuItems as $menuItem) :
			if ($menuItem === end($this->menuItems) || !ArrayHelper::keyExists('items', $menuItem)) :
				$menuItems[] = $menuItem;
				continue;
			endif;
			ArrayHelper::multisort($menuItem['items'], ['label', 'url']);
			$menuItems[] = $menuItem;
		endforeach;

		return $menuItems;
	}

	public function getUrlList($items = null): array {
		foreach ($items ?? self::getItemList() as $item) :
			if (!is_array($item) || ArrayHelper::keyExists('visible', $item))
				continue;

			if (isset($item['url']))
				$pages[] = ArrayHelper::getValue($item, 'url.0');

			if (isset($item['items']))
				$pages[] = self::getUrlList($item['items']);
		endforeach;
		array_walk_recursive($pages, function($val) use (&$list) { $list[] = $val; });
		return $list;
	}

	private function getData(): array {
		return [
			['label' => Yii::$app->icon->show('newspaper', ['class' => 'mr-1']).Html::tag('span', Yii::t('mr42', 'Articles')), 'url' => ['/articles/index'], 'visible' => true],
			['label' => Yii::$app->icon->show('calculator', ['class' => 'mr-1']).Html::tag('span', Yii::t('mr42', 'Calculator')), 'url' => null,
				'items' => [
					['label' => Yii::t('mr42', 'Date (add/subtract)'), 'url' => ['/calculator/date']],
					['label' => Yii::t('mr42', 'Date to Date (duration)'), 'url' => ['/calculator/duration']],
					['label' => Yii::t('mr42', 'Microsoft® Office 365® End Date'), 'url' => ['/calculator/office365']],
					['label' => Yii::t('mr42', 'Time Zone Converter'), 'url' => ['/calculator/timezone']],
					['label' => Yii::t('mr42', 'Week Numbers'), 'url' => ['/calculator/weeknumbers']],
					['label' => Yii::t('mr42', 'Wifi Protected Access Pre-Shared Key'), 'url' => ['/calculator/wpapsk']],
				],
			],
			['label' => Yii::$app->icon->show('wrench', ['class' => 'mr-1']).Html::tag('span', Yii::t('mr42', 'Tools')), 'url' => null,
				'items' => [
					['label' => Yii::t('mr42', 'Barcode Generator'), 'url' => ['/tools/barcode']],
					['label' => Yii::t('mr42', 'Browser Headers'), 'url' => ['/tools/headers']],
					['label' => Yii::t('mr42', 'Country Information'), 'url' => ['/tools/country']],
					['label' => Yii::t('mr42', 'Favicon Converter'), 'url' => ['/tools/favicon']],
					['label' => Yii::t('mr42', 'HTML to Markdown Converter'), 'url' => ['/tools/html-to-markdown']],
					['label' => Yii::t('mr42', 'OUI Lookup'), 'url' => ['/tools/oui']],
					['label' => Yii::t('mr42', 'Password Generator'), 'url' => ['/tools/password']],
					['label' => Yii::t('mr42', 'Phonetic Alphabet Translator'), 'url' => ['/tools/phonetic-alphabet']],
					['label' => Yii::t('mr42', 'QR Code Generator'), 'url' => ['/tools/qr']],
				],
			],
			['label' => Yii::$app->icon->show('music', ['class' => 'mr-1']).Html::tag('span', Yii::t('mr42', 'Music')), 'url' => null,
				'items' => [
					['label' => Yii::t('mr42', 'Lyrics'), 'url' => ['/music/lyrics'], 'visible' => true],
					['label' => Yii::t('mr42', 'Collection'), 'url' => ['/music/collection']],
				],
			],
			['label' => Yii::$app->icon->show('share-alt', ['class' => 'mr-1']).Html::tag('span', Yii::$app->name), 'url' => null,
				'items' => [
					['label' => Yii::t('mr42', 'Contact'), 'url' => ['/site/contact']],
					['label' => Yii::t('mr42', 'My Pi'), 'url' => ['/site/pi']],
				],
			],
			$this->getUserMenu(),
		];
	}

	private function getUserMenu(): array {
		if ($this->isGuest())
			return ['label' => Yii::$app->icon->show('sign-in-alt', ['class' => 'mr-1']).Html::tag('span', Yii::t('usuario', 'Login')), 'url' => ['/user/security/login'], 'visible' => true];

		if ($this->isAdmin()) :
			$subMenu[] = ['label' => Yii::t('mr42', 'Create Article'), 'url' => ['/articles/create']];
			$subMenu[] = ['label' => Yii::t('usuario', 'Manage users'), 'url' => ['/user/admin/index']];
			$subMenu[] = ['label' => Yii::t('mr42', 'PHP {version}', ['version' => PHP_VERSION]), 'url' => ['/site/php']];
			$subMenu[] = Html::tag('div', null, ['class' => 'dropdown-divider']);
		endif;
		$subMenu[] = ['label' => Yii::t('mr42', 'View Profile'), 'url' => ['/user/profile/show', 'username' => Yii::$app->user->identity->username]];
		$subMenu[] = Html::tag('div', null, ['class' => 'dropdown-divider']);
		$subMenu[] = ['label' => Yii::t('usuario', 'Profile settings'), 'url' => ['/user/settings/profile']];
		$subMenu[] = ['label' => Yii::t('usuario', 'Account settings'), 'url' => ['/user/settings/account']];
		$subMenu[] = ['label' => Yii::t('usuario', 'Networks'), 'url' => ['/user/settings/networks']];
		$subMenu[] = Html::tag('div', null, ['class' => 'dropdown-divider']);
		$subMenu[] = ['label' => Yii::t('usuario', 'Logout'), 'url' => ['/user/security/logout'], 'linkOptions' => ['data-method' => 'post']];

		$unread = $this->isAdmin() ? ArticlesComments::find()->where(['not', ['active' => true]])->count() : 0;
		$unreadBadge = $unread > 0 ? Html::tag('sup', $unread, ['class' => 'badge badge-info ml-1']) : '';
		return ['label' => Yii::$app->icon->show('user-circle', ['class' => 'mr-1']).Html::tag('span', Yii::$app->user->identity->username.$unreadBadge), 'url' => null, 'items' => $subMenu];
	}

	private function isAdmin(): bool {
		return !$this->isGuest() && Yii::$app->user->identity->isAdmin;
	}

	private function isGuest(): bool {
		return php_sapi_name() === 'cli' || Yii::$app->controller->action->id === 'sitemap' ?: Yii::$app->user->isGuest;
	}
}
