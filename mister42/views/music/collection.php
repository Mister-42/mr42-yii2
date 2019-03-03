<?php
use yii\helpers\{Html, Url};
use yii\web\View;

$this->title = Yii::t('mr42', 'Collection');
$this->params['breadcrumbs'][] = Yii::t('mr42', 'Music');
$this->params['breadcrumbs'][] = $this->title;

$tabs = [
	'collection' => Yii::t('mr42', 'Collection'),
	'wishlist' => Yii::t('mr42', 'Wishlist'),
];

$this->registerJs(Yii::$app->formatter->jspack('jquery.unveil.js'), View::POS_END);
$this->registerJs('$(\'a[data-toggle="tab"]\').on(\'shown.bs.tab\', function (e) {$(window).trigger("lookup")})', View::POS_END);
$this->registerJs('$("img").unveil();', View::POS_READY);

echo Html::tag('h1', $this->title);

foreach ($tabs as $tab => $tabdesc)
	$tabdata[] = Html::a($tabdesc, "#{$tab}", ['aria-controls' => $tab, 'aria-selected' => ($tab === array_key_first($tabs)) ? 'true' : 'false', 'class' => ($tab === array_key_first($tabs)) ? 'nav-link active' : 'nav-link', 'data-toggle' => 'tab', 'id' => "{$tab}-tab", 'role' => 'tab']);
echo Html::ul($tabdata, ['class' => 'nav nav-tabs', 'id' => 'nav-tabs', 'encode' => false, 'itemOptions' => ['class' => 'nav-item'], 'role' => 'tablist']);

echo Html::beginTag('div', ['class' => 'tab-content']);
	foreach ($tabs as $tab => $tabdesc) :
		echo Html::beginTag('div', ['aria-labelledby' => "{$tab}-tab", 'class' => ($tab === array_key_first($tabs)) ? 'tab-pane fade show active' : 'tab-pane fade', 'id' => $tab, 'role' => 'tabpanel']);
			echo Html::beginTag('div', ['class' => 'site-music-collection']);
				echo Html::beginTag('div', ['class' => 'card-deck']);
					foreach ($model->find()->where(['user_id' => 1, 'status' => $tab])->orderBy(['artist' => SORT_ASC, 'year' => SORT_ASC])->all() as $album) :
						echo Html::beginTag('div', ['class' => 'card mb-3']);
							echo Html::a(
								Html::img("@assets/images/blank.png", ['alt' => "{$album->artist} - {$album->year} - {$album->title}", 'class' => 'card-img-top rounded', 'data-src' => Url::to(['music/collection-cover', 'id' => $album->id])])
							, "https://www.discogs.com/release/{$album->id}");
							echo Html::tag('div', Html::tag('small', $album->title, ['class' => 'card-text mt-auto mx-auto font-weight-bold notranslate']), ['class' => 'card-body d-flex text-center p-2']);
							echo Html::tag('div', Html::tag('small', $album->artist), ['class' => 'card-footer text-center p-2 notranslate']);
						echo Html::endTag('div');
					endforeach;
				echo Html::endTag('div');
			echo Html::endTag('div');
		echo Html::endTag('div');
	endforeach;
echo Html::endTag('div');
