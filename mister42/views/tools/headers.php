<?php
use yii\bootstrap4\Html;

$this->title = 'Browser Headers';
$this->params['breadcrumbs'][] = 'Tools';
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', $this->title);

echo Html::beginTag('div', ['class' => 'site-headers']);
	foreach (Yii::$app->request->headers as $name => $value) :
		if ($name != 'cookie')
			echo Html::tag('div',
				Html::tag('div', Html::tag('strong', $name), ['class' => 'col-md-3']).
				Html::tag('div', $value[0], ['class' => 'col-md-9'])
			, ['class' => 'row']);
	endforeach;
echo Html::endTag('div');
