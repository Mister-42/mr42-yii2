<?php
use yii\helpers\Html;

$this->title = 'Browser Headers';
$this->params['breadcrumbs'][] = 'Tools';
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', Html::encode($this->title));

$x=0;
$headers = apache_request_headers();
foreach ($headers as $header => $value) {
	$x++;
	if ($header != "Cookie") {
		echo '<div class="row">';
		echo '<div class="col-lg-2"><strong>' . $header . '</strong></div>';
		echo '<div class="col-lg-10">' . $value . '</div>';
		echo '</div>';
		if ($x !== count($headers)) echo '<div class="row"><div class="col-lg-12"><hr class="twelve" /></div></div>';
	}
}
