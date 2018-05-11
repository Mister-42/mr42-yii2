<?php
use Yii;
use app\models\Icon;
use app\models\articles\Comments;
use yii\bootstrap4\Html;

$isGuest = Yii::$app->controller->action->id === 'sitemap' ? true : Yii::$app->user->isGuest;
$isAdmin = !$isGuest && Yii::$app->user->identity->isAdmin;
$unread = $isAdmin ? Comments::find()->where(['active' => Comments::STATUS_INACTIVE])->count() : 0;
$unreadBadge = $unread > 0 ? Html::tag('span', $unread, ['class' => 'badge badge-info ml-1']) : '';

return [
	['label' => Icon::show('newspaper', ['class' => 'mr-1']) . 'Articles', 'url' => ['/articles/index'], 'visible' => true],
	['label' => Icon::show('calculator', ['class' => 'mr-1']) . 'Calculator', 'url' => null,
		'items' => [
			['label' => 'Date (add/subtract)', 'url' => ['/calculator/date']],
			['label' => 'Date to Date (duration)', 'url' => ['/calculator/duration']],
			['label' => 'Microsoft® Office 365® End Date', 'url' => ['/calculator/office365']],
			['label' => 'Time Zone Converter', 'url' => ['/calculator/timezone']],
			['label' => 'Wifi Protected Access Pre-Shared Key', 'url' => ['/calculator/wpapsk']],
		],
	],
	['label' => Icon::show('wrench', ['class' => 'mr-1']) . 'Tools', 'url' => null,
		'items' => [
			['label' => 'Barcode Generator', 'url' => ['/tools/barcode']],
			['label' => 'Browser Headers', 'url' => ['/tools/headers']],
			['label' => 'Country Information', 'url' => ['/tools/country']],
			['label' => 'Favicon Converter', 'url' => ['/tools/favicon']],
			['label' => 'HTML to Markdown Converter', 'url' => ['/tools/html-to-markdown']],
			['label' => 'Password Generator', 'url' => ['/tools/password']],
			['label' => 'Phonetic Alphabet Translator', 'url' => ['/tools/phonetic-alphabet']],
			['label' => 'QR Code Generator', 'url' => ['/tools/qr']],
		],
	],
	['label' => Icon::show('music', ['class' => 'mr-1']) . 'Lyrics', 'url' => ['/lyrics/index'], 'visible' => true],
	$isGuest
		?	['label' => Icon::show('sign-in-alt', ['class' => 'mr-1']) . 'Login', 'url' => ['/user/security/login'], 'visible' => true]
		:	['label' => Icon::show('user-circle', ['class' => 'mr-1']) . Yii::$app->user->identity->username . $unreadBadge, 'url' => null,
				'items' => [
					['label' => 'Create Article', 'url' => ['/articles/create'], 'visible' => $isAdmin],
					['label' => 'Manage Users', 'url' => ['/user/admin/index'], 'visible' => $isAdmin],
					['label' => 'PHP ' . phpversion(), 'url' => ['/site/php-version'], 'visible' => $isAdmin],
					$isAdmin ? Html::tag('div', null, ['class' => 'dropdown-divider']) : '',
					['label' => 'View Profile', 'url' => ['/user/profile/show', 'username' => Yii::$app->user->identity->username]],
					Html::tag('div', null, ['class' => 'dropdown-divider']),
					['label' => 'Edit Profile', 'url' => ['/user/settings/profile']],
					['label' => 'Account Settings', 'url' => ['/user/settings/account']],
					['label' => 'Social Networks', 'url' => ['/user/settings/networks']],
					Html::tag('div', null, ['class' => 'dropdown-divider']),
					['label' => 'Logout', 'url' => ['/user/security/logout'], 'linkOptions' => ['data-method' => 'post']],
				],
			],
];
