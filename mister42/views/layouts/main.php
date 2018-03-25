<?php
use app\assets\{AppAsset, ImagesAsset};
use app\models\Menu;
use yii\bootstrap\{Html, Nav, NavBar};
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

AppAsset::register($this);
ImagesAsset::register($this);

$this->beginPage();

echo '<!DOCTYPE html>';
echo '<html lang=' . Yii::$app->language . '>';
echo '<head>';
echo Html::tag('title', Html::encode($this->title));
$this->registerMetaTag(['charset' => Yii::$app->charset]);
$this->registerMetaTag(['name' => 'author', 'content' => Yii::$app->name]);
$this->registerMetaTag(['name' => 'description', 'content' => Html::encode(Yii::$app->params['description'])]);
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1']);
$this->registerLinkTag(['rel' => 'dns-prefetch', 'href' => Yii::getAlias('@assets')]);
if (Yii::$app->controller->id !== 'articles' || Yii::$app->controller->action->id !== 'index')
	$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::current([], true)]);
$this->registerLinkTag(['rel' => 'alternate', 'href' => Url::to(['/feed/rss'], true), 'type' => 'application/rss+xml', 'title' => Yii::$app->name]);
$this->registerLinkTag(['rel' => 'icon', 'sizes' => '16x16 32x32 48x48 64x64', 'type' => 'image/x-icon', 'href' => Yii::$app->assetManager->getBundle('app\assets\ImagesAsset')->baseUrl.'/favicon.ico']);
$this->registerCsrfMetaTags();
$this->head();
echo '</head><body>';

$this->beginBody();
echo '<div class="wrap">';
	NavBar::begin([
		'brandLabel' => Yii::$app->name,
		'brandUrl' => Yii::$app->homeUrl,
		'options' => [
			'class' => 'navbar-default navbar-fixed-top',
		],
	]);

	if (Yii::$app->controller->id !== 'site' || Yii::$app->controller->action->id !== 'offline') {
		echo Nav::widget([
			'activateParents' => true,
			'encodeLabels' => false,
			'items' => Menu::getItemList(),
			'options' => ['class' => 'navbar-nav navbar-right'],
		]);
	}
	NavBar::end();

	echo Html::tag('div',
		Breadcrumbs::widget([
			'homeLink' => ['label' => Yii::$app->name, 'url' => Yii::$app->homeUrl],
			'links' => $this->params['breadcrumbs'] ?? [],
		]) . $content
	, ['class' => 'container']);
echo '</div>';

echo '<footer>';
	echo '<div class="container">';
		echo Html::tag('p', '&copy; 2014-' . date('Y') . ' ' . Yii::$app->name, ['class' => 'pull-left']);
		echo '<p class="pull-right">';
			if (Yii::$app->controller->id !== 'site' || Yii::$app->controller->action->id !== 'offline') {
				if (Yii::$app->user->identity->isAdmin)
					echo Html::a('Validate HTML', 'https://validator.w3.org/nu/?doc=' . rawurlencode(Url::current([], true)), ['class' => 'label label-primary hidden-xs', 'target' => '_blank']) . ' ';
				echo Html::a('Contact', ['/site/contact'], ['class' => 'label label-primary']) . ' ';
				echo Html::a('Changelog', ['/site/changelog'], ['class' => 'label label-primary hidden-xs']) . ' ';
				echo Html::a('RSS', ['/feed/rss'], ['class' => 'label label-warning hidden-xs', 'target' => '_blank']);
			}
		echo '</p>';
	echo '</div>';
	echo Html::a(Html::tag('span', '&nbsp;&nbsp;^&nbsp;&nbsp;', ['title' => 'Scroll to top', 'data-toggle' => 'tooltip', 'data-placement' => 'top']), false, ['id' => 'btn-scrolltop']);
echo '</footer>';
$this->endBody();

echo '</body></html>';
$this->endPage();
