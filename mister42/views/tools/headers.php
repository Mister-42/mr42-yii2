<?php

use yii\bootstrap4\Html;

$this->title = Yii::t('mr42', 'Browser Headers');
$this->params['breadcrumbs'][] = Yii::t('mr42', 'Tools');
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', $this->title);

echo Html::beginTag('div', ['class' => 'site-headers']);
    foreach (apache_request_headers() as $name => $value) {
        if ($name !== 'Cookie') {
            echo Html::tag(
                'div',
                Html::tag('div', Html::tag('strong', $name), ['class' => 'col-md-3']) .
                Html::tag('div', $value, ['class' => 'col-md-9']),
                ['class' => 'row']
            );
        }
    }
echo Html::endTag('div');
