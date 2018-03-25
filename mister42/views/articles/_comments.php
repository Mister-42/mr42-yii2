<?php
use Da\User\Model\User;
use yii\bootstrap\Html;
use yii\widgets\Pjax;

foreach ($comments as $comment) :
	echo '<div>';
		echo '<div class="clearfix">';
			echo Html::tag('div',
				Html::tag('h3', Html::encode($comment->title), ['class' => 'comment-info'])
			, ['class' => 'pull-left']);

			if ($mainmodel->belongsToViewer()):
				echo '<div class="pull-right">';
					Pjax::begin(['enablePushState' => false, 'options' => ['tag' => 'span']]);
						echo $comment->showApprovalButton();
					Pjax::end();

					echo ' ' . Html::a(Html::icon('remove').' Delete', ['commentstatus', 'id' => $comment->id, 'action' => 'delete'], [
						'class' => 'btn btn-xs btn-danger action',
						'data-confirm' => 'Are you sure you want to delete this comment?',
						'data-method' => 'post',
					]);
				echo '</div>';
			endif;
		echo '</div>';
		echo $comment->content;
		if (!empty($comment->user)) {
			$profile = User::find()->where(['id' => $model->user->id])->one();
			$comment->name = empty($profile->name) ? Html::encode($profile->user->username) : Html::encode($profile->name);
			$comment->website = $profile->website;
		}
		echo Html::icon('time', ['class' => 'text-muted']) . ' <time datetime="'.date(DATE_W3C, $comment->created).'">'.Yii::$app->formatter->asRelativeTime($comment->created).'</time>';
		echo ' · ' . Html::icon('user', ['class' => 'text-muted']) . ' <span class="author">' . $comment->name . '</span>';
		if ($mainmodel->author === $comment->user)
			echo Html::tag('span', 'Article Author', ['class' => 'badge']);
		if (!empty($comment->website))
			echo ' · ' . Html::icon('globe', ['class' => 'text-muted']) . ' ' . Html::a($comment->website, $comment->website);
	echo '</div>';
endforeach;
