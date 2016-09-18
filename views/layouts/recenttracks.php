<?php
use yii\helpers\Url;

$this->beginContent('@app/views/layouts/main.php');

$username = explode('/', Url::current());
$url = (Yii::$app->urlManager->parseRequest(Yii::$app->request)[0] === 'user/profile/show') ? '/user/recenttracks/' . end($username) : '/lyrics/recenttracks';
$this->registerJs('(function refresh(){$(\'.recent-tracks .tracks\').load(\'' . $url . '\');setTimeout(refresh,60000)})();');
?>
<div class="row">
	<div class="col-sm-12 col-md-8">
		<?= $content; ?>
	</div>

	<aside class="hidden-sm col-md-4">
		<div class="recent-tracks">
			<h3>Recently Played Tracks</h3>
			<div class="tracks"></div>
		</div>
	</aside>
</div>
<?php $this->endContent(); ?>
