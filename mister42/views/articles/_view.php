<?php
use app\models\user\Profile;
use nezhelskoy\highlight\HighlightAsset;
use yii\bootstrap4\Html;
use yii\helpers\StringHelper;

HighlightAsset::register($this);

echo Html::beginTag('article', ['class' => 'card mb-3']);
	echo Html::beginTag('div', ['class' => 'card-header']);
		echo Html::tag('h4', (Yii::$app->controller->action->id === 'article')
			? $model->title
			: Html::a($model->title, ['article', 'id' => $model->id, 'title' => $model->url])
		, ['class' => 'float-left']);

		echo Html::beginTag('div', ['class' => 'float-right']);
			if ($model->belongsToViewer()) :
				echo Html::a(Yii::$app->icon->show('edit', ['class' => 'mr-1']).Yii::t('mr42', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-sm btn-outline-info ml-1']);
				echo Html::a(Yii::$app->icon->show('trash-alt', ['class' => 'mr-1']).Yii::t('yii', 'Delete'), ['delete', 'id' => $model->id], [
					'class' => 'btn btn-sm btn-outline-danger ml-1',
					'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
					'data-method' => 'post',
				]);
			endif;
			if ($model->active && $model->pdf)
				echo Html::a(Yii::$app->icon->show('file-pdf', ['class' => 'mr-1']).Yii::t('mr42', 'PDF'), ['pdf', 'id' => $model->id, 'title' => $model->url], ['class' => 'btn btn-sm btn-outline-secondary ml-1']);
			if (!$model->active)
				echo Html::tag('span', Yii::$app->icon->show('asterisk', ['class' => 'mr-1']).Yii::t('mr42', 'Draft'), ['class' => 'btn btn-sm btn-warning disabled ml-1']);
		echo Html::endTag('div');
	echo Html::endTag('div');

	echo Html::beginTag('div', ['class' => 'card-body']);
		echo Html::beginTag('div', ['class' => 'card-subtitle text-secondary text-right']);
			echo Yii::t('mr42', 'Posted {date}', ['date' => Yii::$app->formatter->asDate($model->created)]);
			if ($model->updated - $model->created > 3600)
				echo ' · '.Yii::t('mr42', 'Updated {date}', ['date' => Yii::$app->formatter->asDate($model->updated)]);
		echo Html::endTag('div');
		echo Html::beginTag('div', ['class' => 'card-text']);
			echo (Yii::$app->controller->action->id === 'article')
				? str_replace('[readmore]', null, $model->contentParsed)
				: StringHelper::byteSubstr($model->contentParsed, 0, mb_strpos($moel->contentParsed, '[readmore]') ?: StringHelper::byteLength($model->contentParsed), '[readmore]');
		echo Html::endTag('div');
	echo Html::endTag('div');

	if ($model->source || Yii::$app->controller->action->id === 'index') :
		echo Html::beginTag('div', ['class' => 'card-footer text-right']);
			if ($model->source)
				echo Html::a(Yii::t('mr42', 'Source'), $model->source, ['class' => 'btn btn-outline-secondary']);
			if (Yii::$app->controller->action->id === 'index')
				echo Html::a((strpos($model->contentParsed, '[readmore]')) ? Yii::t('mr42', 'Read Full Article') : Yii::t('mr42', 'Read Article').' &raquo;', ['article', 'id' => $model->id, 'title' => $model->url], ['class' => 'btn btn-outline-info ml-2']);
		echo Html::endTag('div');
	endif;

	$profile = Profile::findOne(['user_id' => $model->authorId]);
	if (Yii::$app->controller->action->id === 'article' && !empty($profile->bio) && $author = Profile::show($profile))
		echo Html::tag('div', $author, ['class' => 'card-footer']);

	echo Html::beginTag('div', ['class' => 'card-footer']);
		$bar[] = Yii::$app->icon->show('link', ['class' => 'mr-1 text-muted']).Html::a(Yii::t('mr42', 'Permalink'), ['/permalink/articles', 'id' => $model->id]);

		$commentText = Yii::t('mr42', '{results, plural, =0{no comments yet} =1{1 comment} other{# comments}}', ['results' => count($model->comments)]);
		$bar[] = Yii::$app->icon->show('comment', ['class' => 'mr-1 text-muted']).Html::a($commentText, ['article', 'id' => $model->id, 'title' => $model->url, '#' => 'comments']);

		$tags = StringHelper::explode($model->tags);
		if (count($tags) > 0) :
			foreach ($tags as $tag)
				$tagArray[] = Html::a($tag, ['tag', 'tag' => $tag]);
			$bar[] = Yii::$app->icon->show(count($tags) === 1 ? 'tag' : 'tags', ['class' => 'mr-1 text-muted']).implode(', ', $tagArray);
		endif;

		$bar[] = Yii::$app->icon->show('clock', ['class' => 'mr-1 text-muted']).Html::tag('time', Yii::$app->formatter->asRelativeTime($model->created), ['datetime' => date(DATE_W3C, $model->created)]);

		if ($model->updated - $model->created > 3600)
			$bar[] = Yii::$app->icon->show('sync', ['class' => 'mr-1 text-muted']).Html::tag('time', Yii::$app->formatter->asRelativeTime($model->updated), ['datetime' => date(DATE_W3C, $model->updated)]);

		$bar[] = Yii::$app->icon->show('user', ['class' => 'mr-1 text-muted']).Html::a($profile->name ?? $model->author->username, ['/user/profile/show', 'username' => $model->author->username]);
		echo Html::tag('div', implode(' · ', $bar));
	echo Html::endTag('div');
echo Html::endTag('article');
