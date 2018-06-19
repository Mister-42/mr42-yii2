<?php
use yii\helpers\{Html, Url};

$this->title = Yii::t('mr42', 'Collection');
$this->params['breadcrumbs'][] = Yii::t('mr42', 'Music');
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', $this->title);

echo Html::beginTag('div', ['class' => 'site-music-collection']);
	echo Html::beginTag('div', ['class' => 'card-deck']);
		foreach ($model->find()->orderBy('artist, year')->all() as $album) :
			echo Html::beginTag('div', ['class' => 'card mb-3']);
				echo Html::a(
					Html::img(['music/collection-cover', 'id' => $album->id], ['alt' => "{$album->artist} - {$album->year} - {$album->title}", 'class' => 'card-img-top rounded'])
				, "https://www.discogs.com/release/{$album->id}");
				echo Html::tag('div', Html::tag('small', $album->title, ['class' => 'card-text mt-auto font-weight-bold']), ['class' => 'card-body d-flex text-left']);
				echo Html::tag('div', Html::tag('small', $album->artist), ['class' => 'card-footer text-left']);
			echo Html::endTag('div');
		endforeach;
	echo Html::endTag('div');
echo Html::endTag('div');
