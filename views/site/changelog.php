<?php
use Yii;
use app\models\Feed;
use app\models\General;
use yii\helpers\Html;

$this->title = 'Changelog';
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', Html::encode($this->title));

echo '<div class="site-changelog">';
$limit = (isset(Yii::$app->params['changelogCount']) && is_int(Yii::$app->params['changelogCount'])) ? Yii::$app->params['changelogCount'] : 10;
$items = Feed::find()
	->where(['feed' => 'changelog'])
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
echo '</div>';
