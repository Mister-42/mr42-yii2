<?php
use yii\bootstrap4\Html;

$this->title = Yii::t('mr42', 'Lyrics');
$this->params['breadcrumbs'] = [Yii::t('mr42', 'Music')];
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', $this->title);

echo Html::beginTag('div', ['class' => 'site-lyrics']);
	echo Html::beginTag('div', ['class' => 'row artists']);
		$x = 0;
		foreach ($data as $artist) :
			if ($x === 0 || $x % ceil(count($data) / 4) === 0)
				echo Html::beginTag('div', ['class' => 'col-md-3 text-center text-nowrap']);

			$draft = ($artist->active) ? '' : Html::tag('sup', Yii::t('mr42', 'Draft'), ['class' => 'badge badge-pill badge-warning ml-1']);
			echo Html::a($artist->name.$draft, ['lyrics', 'artist' => $artist->url], ['class' => 'notranslate']);

			if (++$x === count($data) || $x % ceil(count($data) / 4) === 0)
				echo Html::endTag('div');
		endforeach;
	echo Html::endTag('div');
echo Html::endTag('div');
