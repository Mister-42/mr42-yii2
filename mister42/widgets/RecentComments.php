<?php
namespace app\widgets;
use Yii;
use app\models\articles\Comments;
use yii\bootstrap\{Html, Widget};

class RecentComments extends Widget {
	public function run(): string {
		$limit = is_int(Yii::$app->params['recentArticles']) ? Yii::$app->params['recentArticles'] : 5;
		$comments = Comments::find()
			->orderBy('created DESC')
			->with('article')
			->where(['active' => Comments::STATUS_ACTIVE])
			->limit($limit)
			->all();
		return empty($comments) ? Html::tag('p', 'No comments to display.') : Html::tag('ul', self::renderComments($comments), ['class' => 'list-unstyled']);
	}

	private function renderComments(array $comments): string {
		foreach ($comments as $comment) :
			$link = Html::a(Html::encode($comment->title), ['articles/index', 'id' => $comment->article->id, 'title' => $comment->article->url, '#' => 'comments']);
			$items[] = Html::tag('li', $link);
		endforeach;
		return implode($items);
	}
}
