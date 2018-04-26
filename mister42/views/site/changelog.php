<?php
use app\models\site\Changelog;
use yii\bootstrap4\Html;

$this->title = 'Changelog';
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', $this->title);

echo Html::beginTag('div', ['class' => 'site-changelog']);
foreach (Changelog::find()->orderBy('time DESC')->all() as $item) :
	$url = "https://github.com/Thoulah/mr.42/commit/{$item->id}";
	echo Html::tag('div',
		Html::tag('div', Html::a(Yii::$app->formatter->asNText($item->description), $url), ['class' => 'col text-left']) .
		Html::tag('div', Html::tag('time', Yii::$app->formatter->asDatetime($item->time, 'medium'), ['datetime' => date(DATE_W3C, $item->time)]), ['class' => 'col text-right'])
	, ['class' => 'row']);
endforeach;
echo Html::endTag('div');
