<?php
use app\models\General;
use yii\helpers\Html;
use yii\bootstrap\Carousel;

$this->title = Yii::$app->name;

foreach ($posts as $post) {
	$content = '<h2>'.Html::a(Html::encode($post['title']), ['post/index', 'id' => $post['id'], 'title' => $post['title']]).'</h2>';
	$content .= '<div class="row"><div class="col-xs-12">';

	if (strpos($post['content'], '[readmore]')) {
		$buttonText = 'Read full article';
		$post['content'] = substr($post['content'], 0, strpos($post['content'], '[readmore]'));
	} else { $buttonText = 'Read article'; }

	$content .= General::cleanInput($post['content'], 'gfm', true);
	$content .= Html::a($buttonText . ' &raquo;', ['post/index', 'id' => $post['id'], 'title' => $post['title']], ['class' => 'btn btn-default pull-right']);
	$content .= '</div></div>';

	$item[] = ['content' => $content];
}

echo Carousel::widget([
	'clientOptions' => [
		'keyboard' => false,
	],
	'controls' => false,
	'items' => $item,
	'showIndicators' => false
]);
