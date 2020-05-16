<?php

echo $comment->title . PHP_EOL;

echo $comment->content . PHP_EOL;

echo Yii::$app->mr42->createUrl(['/permalink/articles', 'id' => $model->id]);
