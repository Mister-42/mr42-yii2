<?php
use app\models\post\Comment;
use app\models\post\Post;
use app\widgets\Feed;
use app\widgets\Item;
use app\widgets\RecentPosts;
use app\widgets\Search;
use app\widgets\TagCloud;
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
		'options' => ['class' => 'search'],
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
		'header' => Html::tag('h4', 'ScienceDaily'),
	]); ?>
	</aside>
</div>
<?php $this->endContent(); ?>
