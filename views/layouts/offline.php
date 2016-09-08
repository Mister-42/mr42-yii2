<?php
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\helpers\Url;

app\assets\AppAsset::register($this);

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
<?= Html::tag('title', Html::encode($this->title)) ?>
<?= $this->registerMetaTag(['charset' => Yii::$app->charset]) ?>
<?= $this->registerMetaTag(['name' => 'author', 'content' => Yii::$app->name]) ?>
<?= $this->registerMetaTag(['name' => 'description', 'content' => Html::encode(Yii::$app->params['description'])]) ?>
<?= $this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1']) ?>
<?= $this->registerLinkTag(['rel' => 'canonical', 'href' => Url::to(Url::current(), true)]); ?>
<?= $this->registerLinkTag(['rel' => 'alternate', 'href' => Url::to(['/tech/rss'], true), 'type' => 'application/rss+xml', 'title' => Yii::$app->name]) ?>
<?= $this->registerLinkTag(['rel' => 'icon', 'sizes' => '16x16 32x32 48x48 64x64', 'type' => 'image/x-icon', 'href' => Url::to('@assetsUrl/images/'.Yii::$app->params['favicon'])]) ?>
<?= Html::csrfMetaTags() ?>
<?= $this->head() ?></head>
<body>

<?php $this->beginBody() ?>
<div class="wrap">
	<?php NavBar::begin([
		'brandLabel' => Yii::$app->name,
		'brandUrl' => Yii::$app->homeUrl,
		'options' => [
			'class' => 'navbar-fixed-top',
		],		
	]);

	NavBar::end();
	?>

	<div class="container">
		<?= $content ?>
	</div>
</div>

<footer class="footer">
	<div class="container">
		<p class="pull-left">&copy; 2014-<?= date('Y') ?> <?= Yii::$app->name ?></p>
		<p class="pull-right"></p>
	</div>
</footer>
<?php $this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>
