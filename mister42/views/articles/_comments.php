<?php
use app\models\Icon;
use Da\User\Model\User;
use yii\bootstrap4\Html;
use yii\widgets\Pjax;

foreach ($comments as $comment) :
	echo '<div>';
		echo '<div class="clearfix">';
			echo Html::tag('div',
				Html::tag('h4', $comment->title, ['class' => 'comment-info'])
			, ['class' => 'float-left']);

			if ($mainmodel->belongsToViewer()):
				echo '<div class="float-right">';
					Pjax::begin(['enablePushState' => false, 'options' => ['tag' => 'span']]);
						echo $comment->showApprovalButton();
					Pjax::end();

					echo Html::a(Icon::show('trash-alt') . ' Delete', ['commentstatus', 'id' => $comment->id, 'action' => 'delete'], [
						'class' => 'badge badge-danger ml-1',
						'data-confirm' => 'Are you sure you want to delete this comment?',
						'data-method' => 'post',
					]);
				echo '</div>';
			endif;
		echo '</div>';
		echo $comment->content;
		if (!empty($comment->user)) {
			$profile = User::find()->where(['id' => $model->user->id])->one();
			$comment->name = empty($profile->name) ? $profile->user->username : $profile->name;
			$comment->website = $profile->website;
		}
		echo Icon::show('clock', ['class' => 'text-muted']) . ' <time datetime="'.date(DATE_W3C, $comment->created).'">'.Yii::$app->formatter->asRelativeTime($comment->created).'</time>';
		echo ' · ' . Icon::show('user', ['class' => 'text-muted']) . ' <span class="author">' . $comment->name . '</span>';
		if ($mainmodel->author === $comment->user)
			echo Html::tag('span', 'Article Author', ['class' => 'badge']);
		if (!empty($comment->website))
			echo ' · ' . Icon::show('globe', ['class' => 'text-muted']) . ' ' . Html::a($comment->website, $comment->website);
	echo '</div>';
endforeach;
