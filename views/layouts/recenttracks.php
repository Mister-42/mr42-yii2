<?php
use app\widgets\{Item, WeeklyArtistChart};
use yii\bootstrap\Html;
use yii\helpers\Url;

$this->beginContent('@app/views/layouts/main.php');

$url = (Yii::$app->controller->id === 'profile' && Yii::$app->controller->action->id === 'show') ? '/user/recenttracks/' . basename(Url::current()) : '/lyrics/recenttracks';
$this->registerJs('(function refresh(){$(\'aside .tracks\').load(\'' . $url . '\');setTimeout(refresh,60000)})();');
?>
<div class="row">
	<?= Html::tag('div', $content, ['class' => 'col-sm-12 col-md-8']) ?>

	<aside class="hidden-xs hidden-sm col-md-4"><?php
		echo Html::tag('h4', 'Recently Played Tracks');
		echo Html::tag('div', null, ['class' => 'clearfix tracks']);

		if (Yii::$app->controller->id === 'profile' || Yii::$app->controller->action->id === 'show') {
			echo Html::tag('div',
				Item::widget([
					'body' => WeeklyArtistChart::widget(['profile' => basename(Url::current())]),
					'header' => Html::tag('h4', 'Weekly Artist Chart'),
				])
			, ['class' => 'clearfix artists']);
		}
	?></aside>
</div>
<?php $this->endContent();
