<?php
use app\models\site\Changelog;
use yii\bootstrap\Html;

$this->title = 'Changelog';
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', Html::encode($this->title));

echo '<div class="site-changelog">';
foreach (Changelog::find()->orderBy('time DESC')->all() as $item) :
	$url = "https://github.com/Thoulah/mr.42/commit/{$item->id}";
	echo Html::tag('div',
		Html::tag('div', Html::a(Yii::$app->formatter->asNText($item->description), $url), ['class' => 'col-md-9']) .
		Html::tag('div', Html::tag('time', Yii::$app->formatter->asDatetime($item->time, 'medium'), ['datetime' => date(DATE_W3C, $item->time)]), ['class' => 'col-md-3 text-right'])
	, ['class' => 'row']);
endforeach;
echo '</div>';
