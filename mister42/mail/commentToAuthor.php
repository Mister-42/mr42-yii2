<?php
use yii\helpers\Url;

echo $comment->title.PHP_EOL;

echo $comment->content.PHP_EOL;

echo Yii::$app->urlManagerMr42->createUrl(['/permalink/articles', 'id' => $model->id]);
