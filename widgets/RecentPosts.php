<?php
namespace app\widgets;
use Yii;
use app\models\post\Post;
use yii\bootstrap\{Html, Widget};

class RecentPosts extends Widget
{
	public function run()
	{
		$limit = (isset(Yii::$app->params['recentPosts']) && is_int(Yii::$app->params['recentPosts'])) ? Yii::$app->params['recentPosts'] : 5;

		$posts = Post::find()
				->orderBy('created DESC')
				->limit($limit)
				->all();

		echo (empty($posts)) ? Html::tag('p', 'No posts to display.') : Html::tag('ul', $this->renderPosts($posts), ['class' => 'list-unstyled']);		
	}

	public function renderPosts($posts)
	{
		$items = [];
		foreach ($posts as $post) {
			$link = Html::a(Html::encode($post->title), ['post/index', 'id' => $post->id, 'title' => $post->url]);
			$items[] = Html::tag('li', $link);
		}

		return implode('', $items);
	}
}
