<?php
use kop\y2sp\ScrollPager;
use yii\data\ActiveDataProvider;
use yii\widgets\ListView;

$dataProvider = new ActiveDataProvider([
	'query' => $query ?? $model->find()->orderBy('updated DESC'),
	'pagination' => [
		'defaultPageSize' => 1,
	],
]);

switch (Yii::$app->controller->action->id) :
	case 'tag':
	case 'search':
		$this->params['breadcrumbs'][] = ['label' => Yii::t('mr42', 'Articles'), 'url' => ['index']];
	case 'tag':
		$this->title = Yii::t('mr42', '{results, plural, =0{No articles} =1{1 article} other{# articles}} with tag "{tag}"', ['results' => $dataProvider->totalCount, 'tag' => $tag]);
		break;
	case 'search':
		$this->title = Yii::t('mr42', '{results, plural, =0{No search results} =1{1 search result} other{# search results}} for "{query}"', ['results' => $dataProvider->totalCount, 'query' => $q]);
		break;
	default:
		$this->title = Yii::t('mr42', 'Articles');
endswitch;
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
