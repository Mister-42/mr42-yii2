<?php
use yii\bootstrap\Html;

$this->title = 'Browser Headers';
$this->params['breadcrumbs'][] = 'Tools';
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', Html::encode($this->title));

echo Html::beginTag('div', ['class' => 'site-headers']);
	foreach (Yii::$app->request->headers as $name => $value) :
		if ($name != 'cookie')
			echo Html::tag('div',
				Html::tag('div', Html::tag('strong', $name), ['class' => 'col-md-2']) .
				Html::tag('div', $value[0], ['class' => 'col-md-10'])
			, ['class' => 'row']);
	endforeach;
echo Html::endTag('div');
