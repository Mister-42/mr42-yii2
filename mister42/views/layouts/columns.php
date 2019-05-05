<?php
use app\models\articles\{Articles, ArticlesComments, Search};
use app\widgets\{Feed, Item, RecentArticles, RecentChangelog, RecentComments, TagCloud};
use yii\bootstrap4\{ActiveForm, Html};
use yii\caching\ExpressionDependency;
use yii\helpers\Inflector;

$isHome = Yii::$app->controller->id === 'site' && Yii::$app->controller->action->id === 'index';
$dependency = [
	'class' => ExpressionDependency::class,
	'params' => ['articles' => Articles::getLastModified(), 'comments' => ArticlesComments::getLastModified()],
	'reusable' => true,
];

$this->beginContent('@app/views/layouts/main.php');
echo Html::beginTag('div', ['class' => 'row']);
	echo Html::tag('div', $content, ['class' => $isHome ? 'col-12 col-md-8 col-lg-6' : 'col-12 col-md-9']);
	if ($isHome) :
		echo Html::beginTag('aside', ['class' => 'col-3 d-none d-lg-block']);
			echo Item::widget([
				'body' => Feed::widget(['name' => 'ScienceDaily', 'tooltip' => true]),
				'header' => Yii::$app->icon->show('flask', ['class' => 'mr-1']).'ScienceDaily',
			]);

			echo Item::widget([
				'body' => Feed::widget(['name' => 'TomsHardware', 'tooltip' => true]),
				'header' => Yii::$app->icon->show('hammer', ['class' => 'mr-1']).'Tom\'s Hardware',
			]);
		echo Html::endTag('aside');
	endif;

	echo Html::beginTag('aside', ['class' => 'col-4 col-lg-3 d-none d-md-block']);
		$form = ActiveForm::begin(['action' => ['articles/search'], 'method' => 'get', 'options' => ['role' => 'search']]);
		echo $form->field(new Search(), 'keyword', [
				'options' => ['class' => 'form-group mb-2'],
				'template' => '<div class="input-group input-group-sm">{input}'.Html::tag('div', Html::submitButton(Yii::$app->icon->show('search'), ['class' => 'btn btn-outline-info']), ['class' => 'input-group-append'])."</div>{error}",
			])
			->input('search', ['class' => 'form-control', 'name' => 'q', 'placeholder' => Yii::t('mr42', 'Search Articles…'), 'value' => Yii::$app->request->get('q')])
			->label(false);
		ActiveForm::end();

		if ($this->beginCache('articlewidgets', ['dependency' => $dependency, 'duration' => 0, 'enabled' => !YII_DEBUG, 'variations' => [Yii::$app->language]])) :
			$widgets = [
				Yii::t('mr42', 'Recent Articles') => ['class' => RecentArticles::widget(), 'icon' => 'newspaper'],
				Yii::t('mr42', 'Recent Comments') => ['class' => RecentComments::widget(), 'icon' => 'comments'],
				Yii::t('mr42', 'Tag Cloud') => ['class' => TagCloud::widget(), 'icon' => 'tags'],
			];

			foreach ($widgets as $title => $val)
				echo Item::widget([
					'body' => $val['class'],
					'header' => Yii::$app->icon->show($val['icon'], ['class' => 'mr-1']).$title,
					'options' => ['id' => Inflector::slug($title)],
				]);

			$this->endCache();
		endif;

		if ($isHome)
			echo Item::widget([
				'body' => Feed::widget(['limit' => 5, 'name' => 'Mr42Commits']),
				'header' => Yii::$app->icon->show('github', ['class' => 'mr-1', 'prefix' => 'fab fa-']).Yii::t('mr42', 'Changelog'),
			]);
	echo Html::endTag('aside');
echo Html::endTag('div');
$this->endContent();
