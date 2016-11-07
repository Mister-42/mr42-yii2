<?php
use app\models\site\Changelog;
use yii\bootstrap\Html;

$this->title = 'Changelog';
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', Html::encode($this->title));

echo '<div class="site-changelog">';
foreach (Changelog::find()->orderBy('time DESC')->all() as $item) {
	echo '<div class="row">';
	echo Html::tag('div', substr($item['title'], 0, 7), ['class' => 'col-lg-1']);
	echo Html::tag('div', Yii::$app->formatter->asNText($item['description']), ['class' => 'col-lg-8']);
	echo Html::tag('div', Html::tag('time', Yii::$app->formatter->asDatetime($item['time'], 'medium'), ['datetime' => date(DATE_W3C, $item['time'])]), ['class' => 'col-lg-3 text-right']);
	echo '</div>';
}
echo '</div>';
