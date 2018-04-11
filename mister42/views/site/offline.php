<?php
use yii\bootstrap\{Alert, Html};

$this->title = 'Work In Progress';

echo Html::tag('h1', Html::encode($this->title));
echo Alert::widget(['options' => ['class' => 'alert-warning'], 'body' => 'This website is temporarily offline for maintenance.', 'closeButton' => false]);
