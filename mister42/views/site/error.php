<?php
use yii\bootstrap\{Alert, Html};

$this->title = $name;

echo Html::tag('h1', Html::encode($this->title));
echo Alert::widget(['options' => ['class' => 'alert-danger'], 'body' => nl2br(Html::encode($message)), 'closeButton' => false]);
