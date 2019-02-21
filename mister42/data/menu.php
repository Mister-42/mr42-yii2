<?php
use app\models\articles\ArticlesComments;
use yii\bootstrap4\Html;

$isGuest = php_sapi_name() === 'cli' || Yii::$app->controller->action->id === 'sitemap' ? true : Yii::$app->user->isGuest;
$isAdmin = !$isGuest && Yii::$app->user->identity->isAdmin;
$unread = $isAdmin ? ArticlesComments::find()->where(['not', ['active' => true]])->count() : 0;
$unreadBadge = $unread > 0 ? Html::tag('sup', $unread, ['class' => 'badge badge-info ml-1']) : '';

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
	$isGuest
		? ['label' => Yii::$app->icon->show('sign-in-alt', ['class' => 'mr-1']).Html::tag('span', Yii::t('usuario', 'Login')), 'url' => ['/user/security/login'], 'visible' => true]
		:	['label' => Yii::$app->icon->show('user-circle', ['class' => 'mr-1']).Html::tag('span', Yii::$app->user->identity->username.$unreadBadge), 'url' => null,
				'items' => [
					['label' => Yii::t('mr42', 'Create Article'), 'url' => ['/articles/create'], 'visible' => $isAdmin],
					['label' => Yii::t('usuario', 'Manage users'), 'url' => ['/user/admin/index'], 'visible' => $isAdmin],
					['label' => Yii::t('mr42', 'PHP {version}', ['version' => PHP_VERSION]), 'url' => ['/site/php'], 'visible' => $isAdmin],
					$isAdmin ? Html::tag('div', null, ['class' => 'dropdown-divider']) : '',
					['label' => Yii::t('mr42', 'View Profile'), 'url' => ['/user/profile/show', 'username' => Yii::$app->user->identity->username]],
					Html::tag('div', null, ['class' => 'dropdown-divider']),
					['label' => Yii::t('usuario', 'Profile settings'), 'url' => ['/user/settings/profile']],
					['label' => Yii::t('usuario', 'Account settings'), 'url' => ['/user/settings/account']],
					['label' => Yii::t('usuario', 'Networks'), 'url' => ['/user/settings/networks']],
					Html::tag('div', null, ['class' => 'dropdown-divider']),
					['label' => Yii::t('usuario', 'Logout'), 'url' => ['/user/security/logout'], 'linkOptions' => ['data-method' => 'post']],
				],
			],
];
