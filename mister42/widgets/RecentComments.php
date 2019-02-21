<?php
namespace app\widgets;
use Yii;
use app\models\articles\ArticlesComments;
use yii\bootstrap4\{Html, Widget};

class RecentComments extends Widget {
	public $limit = 5;

	public function run(): string {
		$comments = ArticlesComments::find()
			->orderBy(['created' => SORT_DESC])
			->with('article')
			->where(['active' => true])
			->limit($this->limit)
			->all();

		foreach ($comments as $comment) :
			$link = Html::a($comment->title, ['articles/article', 'id' => $comment->article->id, 'title' => $comment->article->url, '#' => 'comments'], ['class' => 'card-link']);
			$items[] = Html::tag('li', $link, ['class' => 'list-group-item text-truncate']);
		endforeach;

		return (!isset($items))
			? Html::tag('div', Yii::t('mr42', 'No Items to Display.'), ['class' => 'ml-2'])
			: Html::tag('ul', implode($items), ['class' => 'list-group list-group-flush']);
	}
}
