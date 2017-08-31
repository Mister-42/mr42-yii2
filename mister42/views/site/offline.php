<?php
use yii\bootstrap\Html;

$this->title = 'Offline';

echo Html::tag('h1', Html::encode($this->title));
echo Html::tag('p', 'This website is temporarily offline for maintenance.');
