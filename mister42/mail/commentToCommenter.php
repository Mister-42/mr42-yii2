<?php

use yii\helpers\Html;

echo Html::tag('p', Yii::t('mr42', 'Hello {name}', ['name' => $comment->name]) . ',');

echo Html::tag('p', Yii::t('mr42', 'You receive this email to confirm you have posted a comment on the article {link}.', ['link' => Html::a($model->title, Yii::$app->urlManagerMr42->createUrl(['/permalink/articles', 'id' => $model->id]))]));
echo Html::tag('p', Yii::t('mr42', 'It will not be visible until approved by an administrator, who are also notified.'));
