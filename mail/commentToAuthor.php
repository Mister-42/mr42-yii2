<?php
use yii\helpers\Url;
?>
<?= $comment->title . PHP_EOL ?>

<?= $comment->content . PHP_EOL ?>

<?= Url::to(['articles/index', 'id' => $model->id], true) ?>
