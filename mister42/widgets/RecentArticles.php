<?php
namespace app\widgets;
use Yii;
use app\models\articles\Articles;
use yii\bootstrap4\{Html, Widget};

class RecentArticles extends Widget {
	public function run(): string {
		$limit = is_int(Yii::$app->params['recentArticles']) ? Yii::$app->params['recentArticles'] : 5;
		$articles = Articles::find()
			->orderBy('updated DESC')
			->limit($limit)
			->all();
		return empty($articles) ? Html::tag('p', 'No articles to display.') : self::renderArticles($articles);
	}

	private function renderArticles(array $articles): string {
		foreach ($articles as $article) :
			$link = Html::a($article->title, ['articles/index', 'id' => $article->id, 'title' => $article->url], ['class' => 'card-link']);
			$items[] = Html::tag('li', $link, ['class' => 'list-group-item']);
		endforeach;
		return Html::tag('ul', implode($items), ['class' => 'list-group list-group-flush']);
	}
}
