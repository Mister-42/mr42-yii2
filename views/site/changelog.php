<?php
use Yii;
use app\models\Feed;
use app\models\General;
use yii\helpers\Html;

$this->title = 'Changelog';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= Html::tag('h1', Html::encode($this->title)) ?>

<?php
	$limit = (isset(Yii::$app->params['rssItemCount']) && is_int(Yii::$app->params['rssItemCount'])) ? Yii::$app->params['rssItemCount'] : 10;
	$items = Feed::find()
		->where(['feed' => 'github'])
		->orderBy('time DESC')
		->limit($limit)
		->all();

	$x=0;
	foreach ($items as $item) {
		$x++;
		echo '<div class="row">';
		echo '<div class="col-lg-3"><time datetime="'.date(DATE_W3C, $item['time']).'">'.General::timeAgo($item['time']).'</time></div>';
		echo '<div class="col-lg-1">'.substr($item['title'], 0, 7).'</div>';
		echo '<div class="col-lg-8">'.$item['description'].'</div>';
		echo '</div>';
		if ($x !== count($items)) echo '<div class="row"><div class="col-lg-12"><hr class="twelve" /></div></div>';
	}
?>
