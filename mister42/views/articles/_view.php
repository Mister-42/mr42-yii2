<?php
use app\models\user\Profile;
use Da\User\Model\Profile as UserProfile;
use nezhelskoy\highlight\HighlightAsset;
use yii\bootstrap4\Html;
use yii\helpers\StringHelper;

HighlightAsset::register($this);

$author = UserProfile::find()->where(['user_id' => $model->user->id])->one();
$authorPage = Html::a($author->name ?? $model->user->username, ['/user/profile/show', 'username' => $model->user->username]);

echo Html::beginTag('article', ['class' => 'mb-3']);
	echo Html::beginTag('div', ['class' => 'clearfix']);
		echo Html::tag('h3', (isset($view) && $view === 'full') ? $model->title : Html::a($model->title, ['index', 'id' => $model->id, 'title' => $model->url]), ['class' => 'float-left']);
		echo Html::beginTag('div', ['class' => 'float-right']);
			if ($model->belongsToViewer()) :
				echo Html::a(Yii::$app->icon->show('edit', ['class' => 'mr-1']).Yii::t('mr42', 'Edit'), ['update', 'id' => $model->id], ['class' => 'badge badge-primary ml-1']);
				echo Html::a(Yii::$app->icon->show('trash-alt', ['class' => 'mr-1']).Yii::t('yii', 'Delete'), ['delete', 'id' => $model->id], [
					'class' => 'badge badge-danger ml-1',
					'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
					'data-method' => 'post',
				]);
			endif;
			if ($model->pdf) :
				echo Html::a(Yii::$app->icon->show('file-pdf', ['class' => 'mr-1']).Yii::t('mr42', 'PDF'), ['pdf', 'id' => $model->id, 'title' => $model->url], ['class' => 'badge badge-warning ml-1']);
			endif;
		echo Html::endTag('div');
	echo Html::endTag('div');

	echo Html::tag('p', Yii::t('mr42', '{date} by {author}', ['date' => Yii::$app->formatter->asDate($model->created), 'author' => $authorPage]), ['class' => 'text-secondary']);

	echo Html::beginTag('div');
		if (strpos($model->content, '[readmore]')) :
			if (isset($view) && $view === 'full') :
				$model->content = str_replace('[readmore]', '', $model->content);
			else :
				$model->content = substr($model->content, 0, strpos($model->content, '[readmore]'));
				$model->content .= Html::tag('div',
					Html::a(Yii::t('mr42', 'Read Full Article').' &raquo;', ['index', 'id' => $model->id, 'title' => $model->url], ['class' => 'float-right btn btn-primary'])
				, ['class' => 'clearfix']);
			endif;
		endif;

		echo $model->content;
	echo Html::endTag('div');

	if (isset($view) && $view === 'full') :
		if ($model->active == 0) :
			$bar[] = Html::tag('div', Yii::t('mr42', 'Not published'), ['class' => 'badge badge-warning']);
		endif;

		$bar[] = Yii::$app->icon->show('link', ['class' => 'mr-1 text-muted']).Html::a(Yii::t('mr42', 'Permalink'), ['/permalink/articles', 'id' => $model->id]);

		$commentText = Yii::t('mr42', '{results, plural, =0{no comments yet} =1{1 comment} other{# comments}}', ['results' => count($model->comments)]);
		$bar[] = Yii::$app->icon->show('comment', ['class' => 'mr-1 text-muted']).Html::a($commentText, ['index', 'id' => $model->id, 'title' => $model->url, '#' => 'comments']);

		$tags = StringHelper::explode($model->tags);
		if (count($tags) > 0) :
			foreach ($tags as $tag) :
				$tagArray[] = Html::a($tag, ['index', 'action' => 'tag', 'q' => $tag]);
			endforeach;
			$bar[] = Yii::$app->icon->show(count($tags) === 1 ? 'tag' : 'tags', ['class' => 'mr-1 text-muted']).implode(', ', $tagArray);
		endif;

		$bar[] = Yii::$app->icon->show('clock', ['class' => 'mr-1 text-muted']).Html::tag('time', Yii::$app->formatter->asRelativeTime($model->created), ['datetime' => date(DATE_W3C, $model->created)]);
		if ($model->updated - $model->created > 3600) :
			$bar[] = Yii::$app->icon->show('sync', ['class' => 'mr-1 text-muted']).Html::tag('time', Yii::$app->formatter->asRelativeTime($model->updated), ['datetime' => date(DATE_W3C, $model->updated)]);
		endif;

		$bar[] = Yii::$app->icon->show('user', ['class' => 'mr-1 text-muted']).Html::tag('span', $authorPage, ['class' => 'author']);

		echo Html::tag('div', implode(' · ', $bar), ['class' => 'article-toolbar my-2']);

		if (!empty($author->bio) && $author = Profile::show($author)) :
			echo Html::tag('div', $author, ['class' => 'alert alert-secondary']);
		endif;
	endif;
echo Html::endTag('article');
