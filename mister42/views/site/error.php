<?php
use yii\bootstrap4\Html;

$this->title = $name;

echo Html::tag('h1', $this->title);
echo Html::tag('div', nl2br($message), ['class' => 'alert alert-danger']);
