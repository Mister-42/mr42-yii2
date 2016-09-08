<?php
use yii\helpers\Html;

$this->title = $name;

echo Html::tag('h1', Html::encode($this->title));
?>
<div class="alert alert-danger"><?= nl2br(Html::encode($message)) ?></div>
