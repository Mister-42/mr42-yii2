<?php
use yii\helpers\Html;
?>
<p>Hello <?= $comment->name ?>,</p>

<p>You receive this email to confirm you have posted a comment on the article "<?= Html::a($model->title, Yii::$app->params['shortDomain']."art{$model->id}") ?>".
It will not be visible until approved by an administrator, who are also notified.</p>
