<?php

use yii\bootstrap4\Html;

$this->title = 'Maintenance Mode';

echo Html::tag('h1', $this->title);
echo Html::tag('div', Yii::t('mr42', 'This website is temporarily offline for maintenance. Please check back later.'), ['class' => 'alert alert-warning']);
