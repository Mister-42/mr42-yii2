<?php

use app\models\user\User;
use yii\bootstrap4\Html;
use yii\widgets\Pjax;

foreach ($data->comments as $comment) {
    echo Html::beginTag('article', ['class' => 'card mb-3']);
    echo Html::beginTag('div', ['class' => 'card-header']);
    echo Html::tag(
        'div',
        Html::tag('h4', $comment->title, ['class' => 'comment-info']),
        ['class' => 'float-left']
    );

    if ($data->belongsToViewer()) {
        echo Html::beginTag('div', ['class' => 'float-right']);
        Pjax::begin(['enablePushState' => false, 'options' => ['tag' => 'span']]);
        echo $comment->showApprovalButton();
        Pjax::end();

        echo Html::a(Yii::$app->icon->name('trash-alt')->class('mr-1') . Yii::t('yii', 'Delete'), ['deletecomment', 'id' => $comment->id], [
                        'class' => 'btn btn-sm btn-outline-danger ml-1',
                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'data-method' => 'post',
                    ]);
        echo Html::endTag('div');
    }
    echo Html::endTag('div');

    echo Html::tag('div', $comment->parsedContent, ['class' => 'card-body']);

    echo Html::beginTag('div', ['class' => 'card-footer']);
    if ($comment->user !== null) {
        $user = User::find(['id' => $comment->user])->with('profile')->one();
        $comment->name = $user->profile->name ?? $user->username;
        $comment->website = $user->profile->website;
    }

    $bar[] = Yii::$app->icon->name('clock')->class('mr-1 text-muted') . Html::tag('time', Yii::$app->formatter->asRelativeTime($comment->created), ['datetime' => date(DATE_W3C, $comment->created)]);
    $bar[] = Yii::$app->icon->name('user')->class('mr-1 text-muted') . ($data->authorId === $comment->user ? Html::tag('span', $comment->name, ['class' => 'ml-1 badge badge-secondary', 'title' => Yii::t('mr42', 'Article Author')]) : $comment->name);
    if (!empty($comment->website)) {
        $bar[] = Yii::$app->icon->name('globe')->class('mr-1 text-muted') . Html::a($comment->website, $comment->website);
    }
    echo Html::tag('div', implode(' · ', $bar));
    unset($bar);
    echo Html::endTag('div');
    echo Html::endTag('article');

    foreach ($comment->commentReplies as $reply) {
        echo Html::beginTag('article', ['class' => 'card mb-3 ml-5']);
        echo Html::beginTag('div', ['class' => 'card-header']);
        echo Html::tag(
            'div',
            Html::tag('h4', "Re: {$comment->title}", ['class' => 'comment-info']),
            ['class' => 'float-left']
        );
        echo Html::endTag('div');

        echo Html::tag('div', $reply->parsedContent, ['class' => 'card-body']);

        echo Html::beginTag('div', ['class' => 'card-footer']);
        if ($reply->user !== null) {
            $user = User::find(['id' => $reply->user])->with('profile')->one();
            $reply->name = $user->profile->name ?? $user->username;
            $reply->website = $user->profile->website;
        }

        $bar[] = Yii::$app->icon->name('clock')->class('mr-1 text-muted') . Html::tag('time', Yii::$app->formatter->asRelativeTime($reply->created), ['datetime' => date(DATE_W3C, $reply->created)]);
        $bar[] = Yii::$app->icon->name('user')->class('mr-1 text-muted') . ($data->authorId === $reply->user ? Html::tag('span', $reply->name, ['class' => 'ml-1 badge badge-secondary', 'title' => Yii::t('mr42', 'Article Author')]) : $reply->name);
        if (!empty($reply->website)) {
            $bar[] = Yii::$app->icon->name('globe')->class('mr-1 text-muted') . Html::a($reply->website, $reply->website);
        }
        echo Html::tag('div', implode(' · ', $bar));
        unset($bar);
        echo Html::endTag('div');
        echo Html::endTag('article');
    }
}
