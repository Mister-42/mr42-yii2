<?php
use app\assets\{AppAsset, ImagesAsset};
use app\models\{Icon, Menu};
use yii\bootstrap4\{Html, Nav, NavBar};
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

AppAsset::register($this);
ImagesAsset::register($this);

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
if (Yii::$app->controller->id !== 'articles' || Yii::$app->controller->action->id !== 'index')
	$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::current([], true)]);
$this->registerLinkTag(['rel' => 'alternate', 'href' => Url::to(['/feed/rss'], true), 'type' => 'application/rss+xml', 'title' => Yii::$app->name]);
$this->registerLinkTag(['rel' => 'icon', 'sizes' => '16x16 32x32 48x48 64x64', 'type' => 'image/x-icon', 'href' => Yii::$app->assetManager->getBundle('app\assets\ImagesAsset')->baseUrl.'/favicon.ico']);
$this->registerCsrfMetaTags();
$this->head();
echo Html::endTag('head');
echo Html::beginTag('body');

$this->beginBody();

echo Html::beginTag('header', ['class' => 'site-header sticky-top']);
NavBar::begin([
	'brandLabel' => Yii::$app->name,
	'brandUrl' => Yii::$app->homeUrl,
	'options' => [
		'class' => 'navbar-dark navbar-expand-md',
	],
]);

	if (Yii::$app->controller->id !== 'site' || Yii::$app->controller->action->id !== 'offline') {
		echo Nav::widget([
			'activateParents' => true,
			'encodeLabels' => false,
			'items' => Menu::getItemList(),
			'options' => ['class' => 'navbar-nav ml-auto'],
		]);
	}
NavBar::end();
echo Html::endTag('header');

echo Html::tag('main',
	Breadcrumbs::widget([
		'activeItemTemplate' => "<li class=\"breadcrumb-item active\">{link}</li>\n",
		'homeLink' => ['label' => Yii::$app->name, 'url' => Yii::$app->homeUrl],
		'itemTemplate' => "<li class=\"breadcrumb-item\">{link}</li>\n",
		'links' => $this->params['breadcrumbs'] ?? [],
	]) .
	$content
, ['class' => 'container position-relative']);

echo Html::beginTag('footer', ['class' => 'fixed-bottom']);
	echo Html::beginTag('div', ['class' => 'container']);
		echo Html::tag('span', '&copy; 2014-' . date('Y') . ' ' . Yii::$app->name);
		echo Html::beginTag('div', ['class' => 'float-right']);
			if (Yii::$app->controller->id !== 'site' || Yii::$app->controller->action->id !== 'offline') {
				if (Yii::$app->user->identity->isAdmin)
					echo Html::a('Validate HTML', 'https://validator.w3.org/nu/?doc=' . rawurlencode(Url::current([], true)), ['class' => 'badge badge-primary ml-1 hidden-xs']);
				echo Html::a('Contact', ['/site/contact'], ['class' => 'badge badge-primary ml-1']);
				echo Html::a('Changelog', ['/site/changelog'], ['class' => 'badge badge-primary ml-1 hidden-xs']);
				echo Html::a('RSS', ['/feed/rss'], ['class' => 'badge badge-warning ml-1 hidden-xs', 'target' => '_blank']);
			}
		echo Html::endTag('div');
	echo Html::endTag('div');
echo Html::endTag('footer');
echo Html::a(Icon::show('chevron-up'), null, ['data-placement' => 'left', 'data-toggle' => 'tooltip', 'id' => 'btn-scrolltop', 'title' => 'Scroll to top']);
$this->endBody();

echo Html::endTag('body');
echo Html::endTag('html');
$this->endPage();
