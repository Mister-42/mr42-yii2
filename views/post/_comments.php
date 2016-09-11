<?php
use app\models\helpers\General;
use dektrium\user\models\User;
use yii\helpers\Html;
use yii\widgets\Pjax;

foreach ($comments as $comment): ?>
	<div class="comment">
		<div class="clearfix">
			<?php  ?>
			<div class="pull-left">
				<h3 class="comment-info"><?= Html::encode($comment->title) ?></h3>
			</div>
			<?php if ($mainmodel->belongsToViewer()): ?>
				<div class="pull-right">
					<?php Pjax::begin(['enablePushState' => false, 'options' => ['tag' => 'span']]);
						echo $comment->showApprovalButton();
					Pjax::end();

					echo ' ' . Html::a('<span class="glyphicon glyphicon-remove"></span> Delete', ['commentstatus', 'id' => $comment->id, 'action' => 'delete'], [
						'class' => 'btn btn-xs btn-danger',
						'data-confirm' => 'Are you sure you want to delete this comment?',
						'data-method' => 'post',
						'style' => 'margin-top:25px;',
					]); ?>
				</div>
			<?php endif; ?>
		</div>

		<?php
		echo General::cleanInput($comment->content, 'gfm-comment');
		if (!empty($comment->user)) {
			$user = new User();
			$profile = $user->finder->findProfileById($comment->user);			
			$comment->name = (empty($profile->name) ? Html::encode($profile->user->username) : Html::encode($profile->name));
			$comment->website = $profile->website;
		}
		echo '<span class="glyphicon glyphicon-time text-muted"></span> <time datetime="'.date(DATE_W3C, $comment->created).'">'.Yii::$app->formatter->asRelativeTime($comment->created).'</time>';
		echo ' &middot; <span class="glyphicon glyphicon-user text-muted"></span> <span class="author">' . $comment->name . '</span>';
		if ($mainmodel->author === $comment->user) { echo ' <span class="badge">Article Author</span>'; }
		echo empty($comment->website) ? '' : ' &middot; <span class="glyphicon glyphicon-globe text-muted"></span> ' . Html::a($comment->website, $comment->website);
		?>
	</div>
<?php endforeach; ?>
