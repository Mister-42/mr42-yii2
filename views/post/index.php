<?php
use kop\y2sp\ScrollPager;
use yii\widgets\ListView;

$results = ($dataProvider->totalCount === 0) ? 'No' : $dataProvider->totalCount;
$articleName = ($results === 1) ? 'article' : 'articles';
$resultName = ($results === 1) ? 'result' : 'results';

switch ($action) {
	case 'tag'		:	$this->params['breadcrumbs'][] = ['label' => 'Articles', 'url' => ['index']];
							$breadcrumb = $results . ' ' . $articleName . ' with tag "' . $tag . '"';
							$this->title = implode(' ∷ ', [$breadcrumb, 'Articles']);
							break;
	case 'search'	:	$this->params['breadcrumbs'][] = ['label' => 'Articles', 'url' => ['index']];
							$breadcrumb = $results . ' search ' . $resultName . ' for "' . $q . '"';
							$this->title = implode(' ∷ ', [$breadcrumb, 'Articles']);
							break;
	default			:	$breadcrumb = 'Articles';
							$this->title = 'Articles';
}
$this->params['breadcrumbs'][] = $breadcrumb;

echo ListView::widget([
	'dataProvider' => $dataProvider,
	'itemOptions' => ['class' => 'item'],
	'itemView' => function ($model, $key, $index, $widget) {
		return $this->render('_view', ['model' => $model]);
	},
	'layout' => '{items}<div class="pager-wrapper pull-right">{pager}</div>',
	'pager' => [
		'class' => ScrollPager::className(),
		'enabledExtensions' => [
			ScrollPager::EXTENSION_TRIGGER,
			ScrollPager::EXTENSION_SPINNER,
			ScrollPager::EXTENSION_PAGING,
		],
		'negativeMargin' => 300,
		'triggerOffset' => false,
	]
]);
