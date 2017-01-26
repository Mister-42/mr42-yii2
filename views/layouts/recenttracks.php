<?php
use app\widgets\{Item, WeeklyArtistChart};
use yii\bootstrap\Html;
use yii\helpers\Url;

$this->beginContent('@app/views/layouts/main.php');

$url = (Yii::$app->controller->id === 'profile' && Yii::$app->controller->action->id === 'show') ? '/user/recenttracks/' . basename(Url::current()) : '/lyrics/recenttracks';
$this->registerJs('(function refresh(){$(\'aside .tracks\').load(\'' . $url . '\');setTimeout(refresh,60000)})();');
?>
<div class="row">
	<div class="col-sm-12 col-md-8">
		<?= $content; ?>
	</div>

	<aside class="hidden-xs hidden-sm col-md-4">
		<?= Html::tag('h4', 'Recently Played Tracks') ?>
		<div class="clearfix tracks"></div>

		<?php if (Yii::$app->controller->id === 'profile' || Yii::$app->controller->action->id === 'show') {
			echo '<div class="clearfix artists">';
			echo Item::widget([
				'body' => WeeklyArtistChart::widget(),
				'header' => Html::tag('h4', 'Weekly Artist Chart'),
			]);
		}
		echo '</div>'; ?>
	</aside>
</div>
<?php $this->endContent(); ?>
