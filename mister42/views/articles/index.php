<?php
use kop\y2sp\ScrollPager;
use yii\data\ActiveDataProvider;
use yii\widgets\ListView;

$dataProvider = new ActiveDataProvider([
	'query' => $query ?? $model->find()->orderBy(['updated' => SORT_DESC]),
	'pagination' => [
		'defaultPageSize' => 2,
	],
]);

$this->title = Yii::t('mr42', 'Articles');
if (Yii::$app->controller->action->id === 'search') :
	$this->title = Yii::t('mr42', '{results, plural, =0{No search results} =1{1 search result} other{# search results}} for "{query}"', ['results' => $dataProvider->totalCount, 'query' => $keyword]);
elseif (Yii::$app->controller->action->id === 'tag') :
	$this->title = Yii::t('mr42', '{results, plural, =0{No articles} =1{1 article} other{# articles}} with tag "{tag}"', ['results' => $dataProvider->totalCount, 'tag' => $tag]);
endif;

if (Yii::$app->controller->action->id !== 'index')
	$this->params['breadcrumbs'][] = ['label' => Yii::t('mr42', 'Articles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo ListView::widget([
	'dataProvider' => $dataProvider,
	'itemOptions' => ['class' => 'item'],
	'itemView' => function($model) {
		return $this->render('_view', ['model' => $model]);
	},
	'layout' => '{items}<div class="pagination float-right">{pager}</div>',
	'pager' => [
		'class' => ScrollPager::class,
		'enabledExtensions' => [
			ScrollPager::EXTENSION_TRIGGER,
			ScrollPager::EXTENSION_SPINNER,
			ScrollPager::EXTENSION_PAGING,
		],
		'negativeMargin' => 300,
		'triggerOffset' => false,
	]
]);
