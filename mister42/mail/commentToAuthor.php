<?php
use yii\helpers\Url;
?>
<?= $comment->title.PHP_EOL ?>

<?= $comment->content.PHP_EOL ?>

<?= Yii::$app->urlManagerAssets->createUrl(['/permalink/articles', 'id' => $model->id]) ?>
