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
		->where(['feed' => 'GitHub'])
		->orderBy('time DESC')
		->limit($limit)
		->all();

	$x=0;
	foreach ($items as $item) {
		$x++;
		echo '<div class="row">';
		echo Html::tag('div', substr($item['title'], 0, 7), ['class' => 'col-lg-1']);
		echo Html::tag('div', $item['description'], ['class' => 'col-lg-8']);
		echo Html::tag('div', Html::tag('time', General::timeAgo($item['time']), ['datetime' => date(DATE_W3C, $item['time'])]), ['class' => 'col-lg-3 text-right']);
		echo '</div>';
	}
?>
