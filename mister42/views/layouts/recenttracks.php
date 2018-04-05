<?php
use app\widgets\{Item, WeeklyArtistChart};
use yii\bootstrap\Html;
use yii\helpers\{Json, Url};

$this->beginContent('@app/views/layouts/main.php');
$this->registerJs('(function refresh(){$(\'aside .tracks\').load(' . Json::htmlEncode('/user/recenttracks/' . basename(Url::current())) . ');setTimeout(refresh,60000)})();');

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::tag('div', $content, ['class' => 'col-sm-12 col-md-8']);

	echo Html::tag('aside',
		Html::tag('h4', 'Recently Played Tracks') .
		Html::tag('div', null, ['class' => 'clearfix tracks']) .
		Html::tag('div',
			Item::widget([
				'body' => WeeklyArtistChart::widget(['profile' => basename(Url::current())]),
				'header' => Html::tag('h4', 'Weekly Artist Chart'),
			])
		, ['class' => 'clearfix artists'])
	, ['class' => 'hidden-xs hidden-sm col-md-4']);
echo Html::endTag('div');

$this->endContent();
