<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<p>Hello <?= Html::encode($comment->name) ?>,</p>

<p>You receive this email to confirm you have posted a comment on the article "<?= Html::a(Html::encode($model->title), Url::to(['post/index', 'id' => $model->id], true)) ?>".
It will not be visible until approved by an administrator, who are also notified.</p>
