<?php
use app\models\General;
use app\models\user\Profile;
use app\models\post\Tags;
use dektrium\user\models\User;
use yii\helpers\Html;
?>
<article class="article">
	<div class="clearfix">
		<div class="pull-left">
			<h2 class="article-title"><?= (isset($view) && $view == 'full') ? Html::encode($model->title) : Html::a(Html::encode($model->title), ['index', 'id' => $model->id, 'title' => $model->title]); ?></h2>
		</div>

		<div class="pull-right">
			<?php if ($model->belongsToViewer()): ?>
				<?= Html::a('<span class="glyphicon glyphicon-edit"></span> Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-xs btn-primary', 'style' => 'margin-top:25px;']) ?>
				<?php echo Html::a('<span class="glyphicon glyphicon-remove"></span> Delete', ['delete', 'id' => $model->id], [
					'class' => 'btn btn-xs btn-danger',
					'data-confirm' => 'Are you sure you want to delete this article?',
					'data-method' => 'post',
					'style' => 'margin-top:25px;',
				]); ?>
			<?php endif; ?>
			<?= Html::a('<span class="glyphicon glyphicon-save"></span> PDF', ['pdf', 'id' => $model->id, 'title' => $model->title], ['class' => 'btn btn-xs btn-warning', 'style' => 'margin-top:25px;']) ?>
		</div>
	</div>

	<div class="article-content">
		<?php
		if (strpos($model->content, '[readmore]')) {
			if (isset($view) && $view == 'full') {
				$model->content = str_replace('[readmore]', '', $model->content);
			} else {
				$model->content = substr($model->content, 0, strpos($model->content, '[readmore]'));
				$model->content .= '<div class="clearfix"><div class="btn btn-default pull-right">'.Html::a('Read full article', ['index', 'id' => $model->id, 'title' => $model->title]).' &raquo;</div></div>';
			}
		}

		echo General::cleanInput($model->content, 'gfm', true);
		?>
	</div>

	<div class="article-info">
		<?php
		if ($model->active == 0)
			echo Html::tag('div', 'Not published.', ['class' => 'well well-sm alert-warning']);

		echo '<span class="glyphicon glyphicon-link text-muted"></span> ' . Html::a('permalink', ['index', 'id' => $model->id]).' &middot; ';
		$commentCount = count($model->comments);
		switch ($commentCount) {
			case 0:	$commentText = 'no comments yet'; break;
			case 1:	$commentText = '1 comment'; break;
			default:	$commentText = $commentCount.' comments';
		}
		$commentText = (count($model->comments) === 1) ? '1 comment' : count($model->comments).' comments';
		echo '<span class="glyphicon glyphicon-comment text-muted"></span> ' . Html::a($commentText, ['index', 'id' => $model->id, 'title' => $model->title, '#' => 'comments']);

		$tags = Tags::string2array($model->tags);
		if (count($tags) > 0) {
			echo ' &middot; ';
			foreach($tags as $tag) {
				$tagArray[] = Html::a($tag, ['index', 'action' => 'tag', 'tag' => $tag]);
			}
			if (count($tags) === 1) {
				echo '<span class="glyphicon glyphicon-tag text-muted"></span>';
			} else {
				echo '<span class="glyphicon glyphicon-tags text-muted"></span>';
			}
			echo ' '.Tags::array2string($tagArray);
		}

		echo ' &middot; <span class="glyphicon glyphicon-time text-muted"></span> <time datetime="'.date(DATE_W3C, $model->created).'">'.Yii::$app->formatter->asRelativeTime($model->created).'</time>';
		if($model->updated - $model->created > 3600)
			echo ' &middot; updated <time datetime="'.date(DATE_W3C, $model->updated).'">'.Yii::$app->formatter->asRelativeTime($model->updated).'</time>';

		$user = new User();
		$profile = $user->finder->findProfileById($model->user->id);
		echo ' &middot; <span class="glyphicon glyphicon-user text-muted"></span> <span class="author">' . (empty($profile->name) ? Html::encode($model->user->username) : Html::encode($profile->name)) . '</span>';

		if (isset($view) && $view == 'full ') {
			if (!empty($profile['bio']) && $author = Profile::show($profile)) {
				echo '<hr />' . Html::tag('div', $author, ['class' => 'well well-sm']);
			}
		}
		?>
	</div>
</article>
