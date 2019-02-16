<?php
use app\models\user\User;
use yii\bootstrap4\Html;
use yii\widgets\Pjax;

foreach ($data->comments as $comment) :
	echo Html::beginTag('article', ['class' => 'card mb-3']);
		echo Html::beginTag('div', ['class' => 'card-header']);
			echo Html::tag('div',
				Html::tag('h4', $comment->title, ['class' => 'comment-info'])
			, ['class' => 'float-left']);

			if ($data->belongsToViewer()) :
				echo Html::beginTag('div', ['class' => 'float-right']);
					Pjax::begin(['enablePushState' => false, 'options' => ['tag' => 'span']]);
						echo $comment->showApprovalButton();
					Pjax::end();

					echo Html::a(Yii::$app->icon->show('trash-alt', ['class' => 'mr-1']).Yii::t('yii', 'Delete'), ['deletecomment', 'id' => $comment->id], [
						'class' => 'btn btn-sm btn-outline-danger ml-1',
						'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
						'data-method' => 'post',
					]);
				echo Html::endTag('div');
			endif;
		echo Html::endTag('div');

		echo Html::tag('div', $comment->parsedContent, ['class' => 'card-body']);

		echo Html::beginTag('div', ['class' => 'card-footer']);
			if (!is_null($comment->user)) :
				$user = User::find(['id' => $comment->user])->with('profile')->one();
				$comment->name = $user->profile->name ?? $user->username;
				$comment->website = $user->profile->website;
			endif;

			$bar[] = Yii::$app->icon->show('clock', ['class' => 'text-muted mr-1']).Html::tag('time', Yii::$app->formatter->asRelativeTime($comment->created), ['datetime' => date(DATE_W3C, $comment->created)]);
			$bar[] = Yii::$app->icon->show('user', ['class' => 'text-muted mr-1']).$comment->name.($data->authorId === $comment->user ? Html::tag('sup', Yii::t('mr42', 'Article Author'), ['class' => 'ml-1 badge badge-secondary']) : '');
			if (!empty($comment->website))
				$bar[] = Yii::$app->icon->show('globe', ['class' => 'text-muted mr-1']).Html::a($comment->website, $comment->website);
			echo Html::tag('div', implode(' · ', $bar));
			unset($bar);
		echo Html::endTag('div');
	echo Html::endTag('article');
endforeach;
