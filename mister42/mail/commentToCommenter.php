<?php
use yii\helpers\{Html, Url};
?>
<p>Hello <?= $comment->name ?>,</p>

<p>You receive this email to confirm you have posted a comment on the article "<?= Html::a($model->title, Url::to(['permalink/articles', 'id' => $model->id])) ?>".
It will not be visible until approved by an administrator, who are also notified.</p>
