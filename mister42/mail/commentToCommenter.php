<?php
use yii\helpers\Html;
?>
<p>Hello <?= Html::encode($comment->name) ?>,</p>

<p>You receive this email to confirm you have posted a comment on the article "<?= Html::a(Html::encode($model->title), "https://mr42.me/art{$model->id}") ?>".
It will not be visible until approved by an administrator, who are also notified.</p>
