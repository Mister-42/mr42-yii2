<?php
namespace app\widgets;
use Yii;
use app\models\articles\Comments;
use yii\bootstrap\{Html, Widget};

class RecentComments extends Widget
{
	public function run()
	{
		$limit = (isset(Yii::$app->params['recentArticles']) && is_int(Yii::$app->params['recentArticles'])) ? Yii::$app->params['recentArticles'] : 5;

		$comments = Comments::find()
			->orderBy('created DESC')
			->with('article')
			->where(['active' => Comments::STATUS_ACTIVE])
			->limit($limit)
			->all();

		echo (empty($comments)) ? Html::tag('p', 'No comments to display.') : Html::tag('ul', $this->renderComments($comments), ['class' => 'list-unstyled']);		
	}

	public function renderComments($comments)
	{
		foreach ($comments as $comment) {
			$link = Html::a(Html::encode($comment->title), ['articles/index', 'id' => $comment->article->id, 'title' => $comment->article->url, '#' => 'comments']);
			$items[] = Html::tag('li', $link);
		}

		return implode($items);
	}
}
