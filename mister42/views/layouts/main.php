<?php

use mister42\assets\AppAsset;
use mister42\models\Menu;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\helpers\Url;

AppAsset::register($this);

$this->beginPage();

echo Html::beginTag('!DOCTYPE', ['html' => true]);
echo Html::beginTag('html', ['lang' => Yii::$app->language]);
echo Html::beginTag('head');
echo Html::tag('title', $this->title);
$this->registerMetaTag(['charset' => Yii::$app->charset]);
$this->registerMetaTag(['name' => 'author', 'content' => Yii::$app->name]);
$this->registerMetaTag(['name' => 'description', 'content' => Yii::t('mr42', 'Sharing beautiful knowledge of the world.')]);
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerLinkTag(['rel' => 'dns-prefetch', 'href' => parse_url(Yii::getAlias('@assets'), PHP_URL_HOST)]);
$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::current([], true)]);
$this->registerLinkTag(['rel' => 'alternate', 'href' => Yii::getAlias('@siteEN') . Url::to(), 'hreflang' => 'x-default']);
foreach (array_keys(Yii::$app->params['languages']) as $lng) {
    if ($lng !== Yii::$app->language) {
        $lngAlias = '@site' . strtoupper($lng);
        $this->registerLinkTag(['rel' => 'dns-prefetch', 'href' => Yii::getAlias($lngAlias)]);
        $this->registerLinkTag(['rel' => 'alternate', 'href' => Yii::getAlias($lngAlias) . Url::to(), 'hreflang' => $lng]);
    }
}
$this->registerLinkTag(['rel' => 'alternate', 'href' => Yii::$app->mr42->createUrl(['/feed/rss']), 'type' => 'application/rss+xml', 'title' => Yii::$app->name]);
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
            'class' => 'navbar-dark navbar-expand-md text-center',
        ],
    ]);

    if (Yii::$app->requestedRoute !== 'site/offline') {
        echo Nav::widget([
            'activateParents' => true,
            'encodeLabels' => false,
            'items' => (new Menu())->getItemList(),
            'options' => ['class' => 'navbar-nav ml-auto'],
        ]);
    }
NavBar::end();
echo Html::endTag('header');

echo Html::tag(
    'main',
    Breadcrumbs::widget([
        'encodeLabels' => false,
        'homeLink' => ['label' => Yii::$app->icon->name('home')->class('mr-1') . Yii::t('yii', 'Home'), 'url' => Yii::$app->homeUrl],
        'links' => $this->params['breadcrumbs'] ?? null,
    ]) .
    $content,
    ['class' => 'container position-relative']
);

echo Html::beginTag('footer', ['class' => 'fixed-bottom']);
    echo Html::beginTag('div', ['class' => 'container d-flex justify-content-between']);
        echo Html::tag('div', Html::tag('span', '&copy; 2014-' . date('Y') . ' ' . Yii::$app->name, ['class' => 'align-middle']));
        echo Html::beginTag('div', ['class' => 'dropup']);
            if (Yii::$app->requestedRoute !== 'site/offline') {
                if (Yii::$app->id === 'mister42-console' || (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin)) {
                    echo Html::a(Yii::$app->icon->name('html5', 'brands'), 'https://validator.w3.org/check/referrer', ['class' => 'badge badge-primary', 'title' => Yii::t('mr42', 'Validate HTML')]);
                }
                echo Html::a(Yii::$app->icon->name('user-secret'), ['/site/privacy'], ['class' => 'badge badge-primary ml-1', 'title' => Yii::t('mr42', 'Privacy Policy')]);
                echo Html::a(Yii::$app->icon->name('rss'), Yii::$app->mr42->createUrl(['/feed/rss']), ['class' => 'badge badge-warning ml-1', 'target' => '_blank', 'title' => Yii::t('mr42', 'RSS')]);
                echo Html::beginTag('div', ['class' => 'btn-group dropup']);
                echo Html::a(Yii::$app->params['languages'][Yii::$app->language]['short'], '', ['aria-expanded' => 'false', 'aria-haspopup' => 'true', 'class' => 'badge badge-info ml-1 dropdown-toggle', 'data-toggle' => 'dropdown', 'title' => Yii::t('mr42', 'Change Language')]);
                echo Html::beginTag('div', ['class' => 'dropdown-menu']);
                foreach (Yii::$app->params['languages'] as $lng => $name) {
                    if ($lng !== Yii::$app->language) {
                        echo Html::a($name['short'], '@site' . strtoupper($lng) . Url::to(), ['class' => 'dropdown-item', 'lang' => $lng, 'title' => $name['full']]);
                    }
                }
                echo Html::endTag('div');
                echo Html::endTag('div');
            }
        echo Html::endTag('div');
    echo Html::endTag('div');
echo Html::endTag('footer');
echo Html::a('&#9650;', null, ['data-placement' => 'left', 'data-toggle' => 'tooltip', 'id' => 'btn-scrolltop', 'class' => 'bg-gradient-dark shadow', 'title' => Yii::t('mr42', 'Scroll to Top')]);
$this->endBody();

echo Html::endTag('body');
echo Html::endTag('html');
$this->endPage();
