<?php
use yii\bootstrap4\Html;

echo Html::tag('article', str_replace('[readmore]', '', $model->content));
