<?php
use app\models\user\Profile;
use Da\User\Model\Profile as UserProfile;
use nezhelskoy\highlight\HighlightAsset;
use yii\bootstrap\Html;
use yii\helpers\StringHelper;

HighlightAsset::register($this);

echo '<article>';
	echo '<div class="clearfix">';
		echo Html::tag('div',
			Html::tag('h2', (isset($view) && $view == 'full') ? Html::encode($model->title) : Html::a(Html::encode($model->title), ['index', 'id' => $model->id, 'title' => $model->url]))
		, ['class' => 'pull-left']);

		echo '<div class="btn-toolbar pull-right">';
			if ($model->belongsToViewer()) {
				echo Html::a(Html::icon('edit') . ' Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-xs btn-primary action']);
				echo Html::a(Html::icon('remove') . ' Delete', ['delete', 'id' => $model->id], [
					'class' => 'btn btn-xs btn-danger action',
					'data-confirm' => 'Are you sure you want to delete this article?',
					'data-method' => 'post',
				]);
			}
			if ($model->pdf)
				echo Html::a(Html::icon('save') . ' PDF', ['pdf', 'id' => $model->id, 'title' => $model->url], ['class' => 'btn btn-xs btn-warning action']);
		echo '</div>';
	echo '</div>';

	echo '<div>';
		if (strpos($model->content, '[readmore]')) {
			if (isset($view) && $view == 'full') {
				$model->content = str_replace('[readmore]', '', $model->content);
			} else {
				$model->content = substr($model->content, 0, strpos($model->content, '[readmore]'));
				$model->content .= '<div class="clearfix"><div class="btn btn-default pull-right">' . Html::a('Read full article', ['index', 'id' => $model->id, 'title' => $model->url]) . ' &raquo;</div></div>';
			}
		}

		echo $model->content;
	echo '</div>';

	echo '<div>';
		if ($model->active == 0)
			echo Html::tag('div', 'Not published', ['class' => 'well well-sm alert-warning']);

		echo Html::icon('link', ['class' => 'text-muted']) . ' ' . Html::a('permalink', "https://mr42.me/art{$model->id}").' · ';
		$commentText = Yii::t('site', '{results, plural, =0{no comments yet} =1{1 comment} other{# comments}}', ['results' => count($model->comments)]);
		echo Html::icon('comment', ['class' => 'text-muted']) . ' ' . Html::a($commentText, ['index', 'id' => $model->id, 'title' => $model->url, '#' => 'comments']);

		$tags = StringHelper::explode($model->tags);
		if (count($tags) > 0) {
			echo ' · ';
			foreach($tags as $tag)
				$tagArray[] = Html::a($tag, ['index', 'action' => 'tag', 'tag' => $tag]);
			echo Html::icon(count($tags) === 1 ? 'tag' : 'tags', ['class' => 'text-muted']);
			echo ' ' . implode(', ', $tagArray);
		}

		echo ' · ' . Html::icon('time', ['class' => 'text-muted']) . ' ' . Html::tag('time', Yii::$app->formatter->asRelativeTime($model->created), ['datetime' => date(DATE_W3C, $model->created)]);
		if($model->updated - $model->created > 3600)
			echo ' · updated ' . Html::tag('time', Yii::$app->formatter->asRelativeTime($model->updated), ['datetime' => date(DATE_W3C, $model->updated)]);

		$profile = UserProfile::find()->where(['user_id' => $model->user->id])->one();
		echo ' · ' . Html::icon('user', ['class' => 'text-muted']) . ' ' . Html::tag('span', empty($profile->name) ? Html::encode($model->user->username) : Html::encode($profile->name), ['class' => 'author']);

		if (isset($view) && $view == 'full' && !empty($profile->bio) && $author = Profile::show($profile))
			echo Html::tag('div', $author, ['class' => 'well well-sm']);
	echo '</div>';
echo '</article>';
