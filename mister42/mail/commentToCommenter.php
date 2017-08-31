<?php
use yii\helpers\{Html, Url};
?>
<p>Hello <?= Html::encode($comment->name) ?>,</p>

<p>You receive this email to confirm you have posted a comment on the article "<?= Html::a(Html::encode($model->title), Url::to(['articles/index', 'id' => $model->id], true)) ?>".
It will not be visible until approved by an administrator, who are also notified.</p>
