<?php
use app\widgets\{Item, WeeklyArtistChart};
use yii\bootstrap4\Html;
use yii\helpers\{Json, Url};

$this->beginContent('@app/views/layouts/main.php');
$this->registerJs('(function refresh(){$(\'aside .tracks\').load('.Json::htmlEncode(Url::to(['/user/recenttracks/'.basename(Url::current())])).');setTimeout(refresh,60000)})();');

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::tag('div', $content, ['class' => 'col-12 col-lg-8']);

	echo Html::tag('aside',
		Html::tag('div',
			Html::tag('div', Yii::t('mr42', 'Recently Played Tracks'), ['class' => 'card-header']).
			Html::tag('div', Html::tag('span', Yii::t('mr42', 'Loading…'), ['class' => 'mx-2']), ['class' => 'tracks'])
		, ['class' => 'card mb-2']).
		Html::tag('div',
			Item::widget([
				'body' => WeeklyArtistChart::widget(['profile' => basename(Url::current())]),
				'header' => Yii::t('mr42', 'Weekly Artist Chart'),
			])
		, ['class' => 'artists'])
	, ['class' => 'd-none d-lg-inline col-4']);
echo Html::endTag('div');

$this->endContent();
