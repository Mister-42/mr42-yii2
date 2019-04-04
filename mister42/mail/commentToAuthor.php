<?php
use yii\helpers\Url;
?>
<?= $comment->title.PHP_EOL ?>

<?= $comment->content.PHP_EOL ?>

<?= Yii::$app->urlManagerMr42->createUrl(['/permalink/articles', 'id' => $model->id]) ?>
