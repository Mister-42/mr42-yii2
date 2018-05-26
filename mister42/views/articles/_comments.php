<?php
use Da\User\Model\User;
use yii\bootstrap4\Html;
use yii\widgets\Pjax;

foreach ($comments as $comment) :
	echo Html::beginTag('div');
		echo Html::beginTag('div', ['class' => 'clearfix']);
			echo Html::tag('div',
				Html::tag('h4', $comment->title, ['class' => 'comment-info'])
			, ['class' => 'float-left']);

			if ($mainmodel->belongsToViewer()) :
				echo Html::beginTag('div', ['class' => 'float-right']);
					Pjax::begin(['enablePushState' => false, 'options' => ['tag' => 'span']]);
						echo $comment->showApprovalButton();
					Pjax::end();

					echo Html::a(Yii::$app->icon->show('trash-alt', ['class' => 'mr-1']).Yii::t('yii', 'Delete'), ['commentstatus', 'id' => $comment->id, 'action' => 'delete'], [
						'class' => 'badge badge-danger ml-1',
						'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
						'data-method' => 'post',
					]);
				echo Html::endTag('div');
			endif;
		echo Html::endTag('div');
		echo $comment->content;
		if (!is_null($comment->user)) :
			$profile = User::find()->where(['id' => $model->user->id])->one();
			$comment->name = $profile->name ?? $profile->user->username;
			$comment->website = $profile->website;
		endif;

		$bar[] = Yii::$app->icon->show('clock', ['class' => 'text-muted mr-1']).Html::tag('time', Yii::$app->formatter->asRelativeTime($comment->created), ['datetime' => date(DATE_W3C, $comment->created)]);
		$bar[] = Yii::$app->icon->show('user', ['class' => 'text-muted mr-1']).$comment->name.($mainmodel->author === $comment->user ? Html::tag('span', Yii::t('mr42', 'Article Author'), ['class' => 'badge badge-secondary']) : '');
		if (!empty($comment->website)) :
			$bar[] = Yii::$app->icon->show('globe', ['class' => 'text-muted mr-1']).Html::a($comment->website, $comment->website);
		endif;
		echo Html::tag('div', implode(' · ', $bar), ['class' => 'article-toolbar my-2']);
		unset($bar);
	echo Html::endTag('div');
endforeach;
