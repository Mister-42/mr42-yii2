<?php
namespace app\widgets;
use Yii;
use app\models\articles\Articles;
use yii\bootstrap\{Html, Widget};

class RecentArticles extends Widget {
	public function run(): string {
		$limit = is_int(Yii::$app->params['recentArticles']) ? Yii::$app->params['recentArticles'] : 5;
		$articles = Articles::find()
			->orderBy('created DESC')
			->limit($limit)
			->all();
		return empty($articles) ? Html::tag('p', 'No articles to display.') : Html::tag('ul', self::renderArticles($articles), ['class' => 'list-unstyled']);
	}

	private function renderArticles(array $articles): string {
		foreach ($articles as $article) :
			$link = Html::a(Html::encode($article->title), ['articles/index', 'id' => $article->id, 'title' => $article->url]);
			$items[] = Html::tag('li', $link);
		endforeach;
		return implode($items);
	}
}
