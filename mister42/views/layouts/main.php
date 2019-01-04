<?php
use app\assets\AppAsset;
use app\models\Menu;
use yii\bootstrap4\{Html, Nav, NavBar};
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

AppAsset::register($this);

$this->beginPage();

echo Html::beginTag('!DOCTYPE', ['html' => true]);
echo Html::beginTag('html', ['lang' => Yii::$app->language]);
echo Html::beginTag('head');
echo Html::tag('title', $this->title);
$this->registerMetaTag(['charset' => Yii::$app->charset]);
$this->registerMetaTag(['name' => 'author', 'content' => Yii::$app->name]);
$this->registerMetaTag(['name' => 'description', 'content' => Yii::$app->params['description']]);
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerLinkTag(['rel' => 'dns-prefetch', 'href' => Yii::getAlias('@assets')]);
$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::current([], true)]);
$this->registerLinkTag(['rel' => 'alternate', 'href' => Url::current(['language' => ''], true), 'hreflang' => 'x-default']);
foreach (array_keys(Yii::$app->params['languages']) as $lng) :
	$this->registerLinkTag(['rel' => 'alternate', 'href' => Url::current(['language' => $lng], true), 'hreflang' => $lng]);
endforeach;
$this->registerLinkTag(['rel' => 'alternate', 'href' => Url::to(['/feed/rss'], true), 'type' => 'application/rss+xml', 'title' => Yii::$app->name]);
$this->registerLinkTag(['rel' => 'icon', 'sizes' => '64x64 48x48 32x32 16x16', 'type' => 'image/x-icon', 'href' => Url::to('@assets/images/favicon.ico')]);
$this->registerLinkTag(['rel' => 'mask-icon', 'color' => Yii::$app->params['themeColor'], 'type' => 'image/x-icon', 'href' => Url::to('@assets/images/safari-pinned-tab.svg')]);
$this->registerLinkTag(['rel' => 'apple-touch-icon', 'sizes' => '180x180', 'href' => Url::to('@assets/images/apple-touch-icon.png')]);
$this->registerLinkTag(['rel' => 'manifest', 'href' => Url::to(['/site/webmanifest'])]);
$this->registerMetaTag(['name' => 'msapplication-config', 'content' => Url::to(['/site/browserconfigxml'])]);
$this->registerMetaTag(['name' => 'theme-color', 'content' => $this->params['themeColor'] ?? Yii::$app->params['themeColor']]);
$this->registerCsrfMetaTags();
$this->head();
echo Html::endTag('head');
echo Html::beginTag('body');

$this->beginBody();

echo Html::beginTag('header', ['class' => 'site-header fixed-top']);
NavBar::begin([
	'brandLabel' => Yii::$app->name,
	'brandUrl' => Yii::$app->homeUrl,
	'options' => [
		'class' => 'navbar-dark navbar-expand-md',
	],
]);

	if (Yii::$app->controller->id !== 'site' || Yii::$app->controller->action->id !== 'offline') :
		echo Nav::widget([
			'activateParents' => true,
			'encodeLabels' => false,
			'items' => Menu::getItemList(),
			'options' => ['class' => 'navbar-nav ml-auto'],
		]);
	endif;
NavBar::end();
echo Html::endTag('header');

echo Html::tag('main',
	Breadcrumbs::widget([
		'activeItemTemplate' => Html::tag('li', '{link}', ['class' => 'breadcrumb-item active']),
		'encodeLabels' => false,
		'homeLink' => ['label' => Yii::$app->icon->show('home', ['class' => 'mr-1']).Yii::$app->name, 'url' => Yii::$app->homeUrl],
		'itemTemplate' => Html::tag('li', '{link}', ['class' => 'breadcrumb-item']),
		'links' => $this->params['breadcrumbs'] ?? null,
	]).
	$content
, ['class' => 'container position-relative']);

echo Html::beginTag('footer', ['class' => 'fixed-bottom']);
	echo Html::beginTag('div', ['class' => 'container']);
		echo Html::tag('div', Html::tag('span', '&copy; 2014-'.date('Y').' '.Yii::$app->name, ['class' => 'align-middle']), ['class' => 'float-left']);
		echo Html::beginTag('div', ['class' => 'float-right dropup']);
			if (Yii::$app->controller->id !== 'site' || Yii::$app->controller->action->id !== 'offline') :
				echo Html::a(Yii::$app->icon->show('envelope'), ['/site/contact'], ['class' => 'badge badge-primary ml-1', 'title' => Yii::t('mr42', 'Contact')]);

				if (php_sapi_name() !== 'cli' && !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin) :
					echo Html::a(Yii::$app->icon->show('html5', ['prefix' => 'fab fa-']), 'https://validator.w3.org/nu/?doc='.rawurlencode(Url::current([], true)), ['class' => 'badge badge-primary ml-1 hidden-xs', 'title' => Yii::t('mr42', 'Validate HTML')]);
				endif;
				echo Html::a(Yii::$app->icon->show('raspberry-pi', ['prefix' => 'fab']), ['/site/pi'], ['class' => 'badge badge-primary ml-1', 'title' => Yii::t('mr42', Yii::t('mr42', 'My Raspberry Pi'))]);
				echo Html::a(Yii::$app->icon->show('rss'), ['/feed/rss'], ['class' => 'badge badge-warning ml-1 hidden-xs', 'target' => '_blank', 'title' => Yii::t('mr42', 'RSS')]);

				echo Html::a(Yii::$app->icon->show('language'), '#', ['aria-expanded' => 'false', 'aria-haspopup' => 'true', 'class' => 'badge badge-info ml-1 dropdown-toggle', 'data-toggle' => 'dropdown', 'id' => 'dropupLanguage', 'role' => 'button', 'title' => Yii::t('mr42', 'Change Language')]);
				echo Html::beginTag('div', ['aria-labelledby' => 'dropupLanguage', 'class' => 'dropdown-menu']);
					foreach (Yii::$app->params['languages'] as $lng => $desc) :
						echo ($lng === Yii::$app->language)
							? Html::tag('div', $desc.Yii::$app->icon->show('check', ['class' => 'ml-1']), ['class' => 'disabled dropdown-item'])
							: Html::a($desc, Url::current(['language' => $lng]), ['class' => 'dropdown-item']);
					endforeach;
				echo Html::endTag('div');
			endif;
		echo Html::endTag('div');
	echo Html::endTag('div');
echo Html::endTag('footer');
echo Html::a(Yii::$app->icon->show('chevron-up'), null, ['data-placement' => 'left', 'data-toggle' => 'tooltip', 'id' => 'btn-scrolltop', 'title' => Yii::t('mr42', 'Scroll to Top')]);
$this->endBody();

echo Html::endTag('body');
echo Html::endTag('html');
$this->endPage();
