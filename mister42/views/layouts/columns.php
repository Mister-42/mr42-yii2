<?php
use app\models\articles\{Articles, Comments};
use app\widgets\{Feed, Item, RecentArticles, RecentChangelog, RecentComments, TagCloud};
use yii\base\DynamicModel;
use yii\bootstrap4\{ActiveForm, Html};
use yii\caching\DbDependency;

$isHome = Yii::$app->controller->id === 'site' && Yii::$app->controller->action->id === 'index';
$search = new DynamicModel(['search']);
$search->addRule('search', 'required');
$search->addRule('search', 'string', ['min' => 3, 'max' => 25]);
$dependency = [
	'class' => DbDependency::class,
	'reusable' => true,
	'sql' => 'SELECT GREATEST(
		IFNULL((SELECT MAX(updated) FROM '.Articles::tableName().' WHERE `active` = '.Articles::STATUS_ACTIVE.'), 1),
		IFNULL((SELECT MAX(created) FROM '.Comments::tableName().' WHERE `active` = '.Comments::STATUS_ACTIVE.'), 1)
	)',
];

$this->beginBlock('widgets');
	echo Item::widget([
		'body' => Feed::widget(['limit' => 5, 'name' => 'Mr42Commits']),
		'header' => Yii::$app->icon->show('github', ['class' => 'mr-1', 'prefix' => 'fab fa-']).Yii::t('mr42', 'Changelog'),
	]);

	echo Item::widget([
		'body' => Feed::widget(['name' => 'ScienceDaily', 'tooltip' => true]),
		'header' => Yii::$app->icon->show('flask', ['class' => 'mr-1']).Yii::t('mr42', 'Science News'),
	]);
$this->endBlock();

$this->beginContent('@app/views/layouts/main.php');
echo Html::beginTag('div', ['class' => 'row']);
	echo Html::tag('div', $content, ['class' => $isHome ? 'col-12 col-md-8 col-lg-6' : 'col-12 col-md-9']);
	if ($isHome) :
		echo Html::tag('aside', $this->blocks['widgets'], ['class' => 'col-3 d-none d-lg-block']);
	endif;
	echo Html::beginTag('aside', ['class' => 'col-4 col-lg-3 d-none d-md-block']);
		$form = ActiveForm::begin(['action' => ['articles/index', 'action' => 'search'], 'id' => 'search', 'method' => 'get', 'options' => ['role' => 'search']]);
		echo $form->field($search, 'search', [
				'options' => ['class' => 'form-group mb-2'],
				'template' => '<div class="input-group input-group-sm">{input}'.Html::tag('div', Html::submitButton(Yii::$app->icon->show('search'), ['class' => 'btn btn-outline-primary']), ['class' => 'input-group-append'])."</div>{error}",
			])
			->label(false)
			->input('search', ['class' => 'form-control', 'name' => 'q', 'placeholder' => Yii::t('mr42', 'Search Articles…'), 'value' => Yii::$app->request->get('q')]);
		ActiveForm::end();

		if ($this->beginCache('articlewidgets-'.Yii::$app->language, ['dependency' => $dependency, 'duration' => 0])) :
			echo Item::widget([
				'body' => RecentArticles::widget(),
				'header' => Yii::$app->icon->show('pencil-alt', ['class' => 'mr-1']).Yii::t('mr42', 'Latest Updates'),
				'options' => ['id' => 'latestArticles'],
			]);

			echo Item::widget([
				'body' => RecentComments::widget(),
				'header' => Yii::$app->icon->show('comments', ['class' => 'mr-1']).Yii::t('mr42', 'Latest Comments'),
				'options' => ['id' => 'latestComments'],
			]);

			echo Item::widget([
				'body' => TagCloud::widget(),
				'header' => Yii::$app->icon->show('tags', ['class' => 'mr-1']).Yii::t('mr42', 'Tag Cloud'),
				'options' => ['id' => 'tags'],
			]);

			$this->endCache();
		endif;

		if (!$isHome) :
			echo $this->blocks['widgets'];
		endif;
	echo Html::endTag('aside');
echo Html::endTag('div');
$this->endContent();
