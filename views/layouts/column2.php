<?php
use app\models\post\{Comment, Post};
use app\widgets\{Feed, Item, RecentPosts, Search, TagCloud};
use yii\bootstrap\Html;
use yii\caching\DbDependency;

$this->beginContent('@app/views/layouts/main.php');

$dependency = [
	'class' => DbDependency::className(),
	'reusable' => true,
	'sql' => 'SELECT GREATEST(
		IFNULL((SELECT MAX(updated) FROM '.Post::tableName().' WHERE `active` = '.Post::STATUS_ACTIVE.'), 1),
		IFNULL((SELECT MAX(created) FROM '.Comment::tableName().' WHERE `active` = '.Post::STATUS_ACTIVE.'), 1)
	)',
];
?>
<div class="row">
	<div class="col-xs-12 col-sm-9">
		<?= $content; ?>
	</div>

	<aside class="hidden-xs col-sm-3">
	<?php echo Item::widget([
		'body' => Search::widget(),
	]);

	if ($this->beginCache('postwidgets', ['dependency' => $dependency, 'duration' => 0])) {
		echo Item::widget([
			'body' => RecentPosts::widget(),
			'header' => Html::tag('h4', 'Latest Articles'),
		]);

		echo Item::widget([
			'body' => TagCloud::widget(),
			'header' => Html::tag('h4', 'Tags'),
		]);

		$this->endCache();
	}

	echo Item::widget([
		'body' => Feed::widget(['name' => 'ScienceDaily']),
		'header' => Html::tag('h4', 'Science News'),
	]); ?>
	</aside>
</div>
<?php $this->endContent(); ?>
