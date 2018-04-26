<?php
use app\models\Icon;
use app\models\user\Profile;
use Da\User\Model\Profile as UserProfile;
use nezhelskoy\highlight\HighlightAsset;
use yii\bootstrap4\Html;
use yii\helpers\StringHelper;

HighlightAsset::register($this);

echo Html::beginTag('article');
	echo Html::beginTag('div', ['class' => 'clearfix']);
		echo Html::tag('div',
			Html::tag('h3', (isset($view) && $view == 'full') ? $model->title : Html::a($model->title, ['index', 'id' => $model->id, 'title' => $model->url]))
		, ['class' => 'float-left']);

		echo '<div class="float-right">';
			if ($model->belongsToViewer()) {
				echo Html::a(Icon::show('edit') . ' Edit', ['update', 'id' => $model->id], ['class' => 'badge badge-primary ml-1']);
				echo Html::a(Icon::show('trash-alt') . ' Delete', ['delete', 'id' => $model->id], [
					'class' => 'badge badge-danger ml-1',
					'data-confirm' => 'Are you sure you want to delete this article?',
					'data-method' => 'post',
				]);
			}
			if ($model->pdf)
				echo Html::a(Icon::show('file-pdf') . ' PDF', ['pdf', 'id' => $model->id, 'title' => $model->url], ['class' => 'badge badge-warning ml-1']);
		echo Html::endTag('div');
	echo Html::endTag('div');

	echo Html::beginTag('div');
		if (strpos($model->content, '[readmore]')) {
			if (isset($view) && $view == 'full') {
				$model->content = str_replace('[readmore]', '', $model->content);
			} else {
				$model->content = substr($model->content, 0, strpos($model->content, '[readmore]'));
				$model->content .= '<div class="clearfix"><div class="btn btn-default pull-right">' . Html::a('Read full article', ['index', 'id' => $model->id, 'title' => $model->url]) . ' &raquo;</div></div>';
			}
		}

		echo $model->content;
	echo Html::endTag('div');

	echo Html::beginTag('div');
		if ($model->active == 0)
			echo Html::tag('div', 'Not published', ['class' => 'badge badge-warning']);

		echo Icon::show('link', ['class' => 'text-muted']) . ' ' . Html::a('permalink', Yii::$app->params['shortDomain']."art{$model->id}").' · ';
		$commentText = Yii::t('site', '{results, plural, =0{no comments yet} =1{1 comment} other{# comments}}', ['results' => count($model->comments)]);
		echo Icon::show('comment', ['class' => 'text-muted']) . ' ' . Html::a($commentText, ['index', 'id' => $model->id, 'title' => $model->url, '#' => 'comments']);

		$tags = StringHelper::explode($model->tags);
		if (count($tags) > 0) {
			foreach($tags as $tag)
				$tagArray[] = Html::a($tag, ['index', 'action' => 'tag', 'tag' => $tag]);
			echo ' · ' . Icon::show(count($tags) === 1 ? 'tag' : 'tags', ['class' => 'text-muted']) . ' ' . implode(', ', $tagArray);
		}

		echo ' · ' . Icon::show('clock', ['class' => 'text-muted']) . ' ' . Html::tag('time', Yii::$app->formatter->asRelativeTime($model->created), ['datetime' => date(DATE_W3C, $model->created)]);
		if($model->updated - $model->created > 3600)
			echo ' · updated ' . Html::tag('time', Yii::$app->formatter->asRelativeTime($model->updated), ['datetime' => date(DATE_W3C, $model->updated)]);

		$profile = UserProfile::find()->where(['user_id' => $model->user->id])->one();
		echo ' · ' . Icon::show('user', ['class' => 'text-muted']) . ' ' . Html::tag('span', empty($profile->name) ? $model->user->username : $profile->name, ['class' => 'author']);

		if (isset($view) && $view == 'full' && !empty($profile->bio) && $author = Profile::show($profile))
			echo Html::tag('div', $author, ['class' => 'alert alert-secondary']);
	echo Html::endTag('div');
echo Html::endTag('article');
