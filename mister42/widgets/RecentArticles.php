<?php
namespace app\widgets;
use Yii;
use app\models\articles\Articles;
use yii\bootstrap4\{Html, Widget};

class RecentArticles extends Widget {
	public $limit = 5;

	public function run(): string {
		$articles = Articles::find()
			->orderBy('updated DESC')
			->limit($this->limit)
			->where(['active' => true])
			->all();
		return empty($articles) ? Html::tag('div', Yii::t('mr42', 'No Items to Display.'), ['class' => 'ml-2']) : self::renderArticles($articles);
	}

	private function renderArticles(array $articles): string {
		foreach ($articles as $article) :
			$link = Html::a($article->title, ['articles/index', 'id' => $article->id, 'title' => $article->url], ['class' => 'card-link']);
			$items[] = Html::tag('li', $link, ['class' => 'list-group-item text-truncate']);
		endforeach;
		return Html::tag('ul', implode($items), ['class' => 'list-group list-group-flush']);
	}
}
