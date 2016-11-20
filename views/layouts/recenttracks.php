<?php
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
		<h4>Recently Played Tracks</h4>
		<div class="tracks"></div>
	</aside>
</div>
<?php $this->endContent(); ?>
