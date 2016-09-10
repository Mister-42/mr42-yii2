<?php
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\MenuItems;
use yii\widgets\Breadcrumbs;

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
<?= $this->registerLinkTag(['rel' => 'canonical', 'href' => Url::to(Url::current(), true)]) ?>
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

	echo Nav::widget([
		'dropDownCaret' => '<strong class="caret"></strong>',
		'options' => ['class' => 'navbar-nav navbar-right'],
		'items' => MenuItems::menuArray(),
	]);

	NavBar::end();
	?>

	<div class="container">
		<?= Breadcrumbs::widget([
			'homeLink' => ['label' => Yii::$app->name, 'url' => Yii::$app->homeUrl],
			'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
			'tag' => 'ol',
		]) ?>
		<?= $content ?>
	</div>
</div>

<footer class="footer">
	<div class="container">
		<p class="pull-left">&copy; 2014-<?= date('Y') ?> <?= Yii::$app->name ?></p>
		<p class="pull-right">
			<?= Html::a('Contact', ['/site/contact'], ['class' => 'label label-primary']) ?>
			<?= Html::a('Credits', ['/site/credits'], ['class' => 'label label-primary']) ?>
			<?= Html::a('Changelog', ['/site/changelog'], ['class' => 'label label-primary visible-md-inline visible-lg-inline']) ?>
			<?= Html::a('RSS', ['/tech/rss'], ['class' => 'label label-warning visible-md-inline visible-lg-inline', 'target' => '_blank']) ?>
		</p>
	</div>
	<?php echo Html::a(Html::tag('span', '&nbsp;&nbsp;^&nbsp;&nbsp;', ['title' => 'Scroll to top', 'data-toggle' => 'tooltip', 'data-placement' => 'top']), false, ['id' => 'btn-scrolltop']); ?>
</footer>
<?php $this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>
