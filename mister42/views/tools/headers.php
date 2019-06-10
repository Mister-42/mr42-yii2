<?php
use yii\bootstrap4\Html;

$this->title = Yii::t('mr42', 'Browser Headers');
$this->params['breadcrumbs'][] = Yii::t('mr42', 'Tools');
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', $this->title);

echo Html::beginTag('div', ['class' => 'site-headers']);
	foreach (Yii::$app->request->headers as $name => $value)
		if ($name !== 'cookie')
			echo Html::tag('div',
				Html::tag('div', Html::tag('strong', $name), ['class' => 'col-md-3']).
				Html::tag('div', $value[0], ['class' => 'col-md-9'])
			, ['class' => 'row']);
echo Html::endTag('div');
