<?php
use app\models\Icon;
use app\models\articles\{Articles, Comments};
use app\widgets\{Feed, Item, RecentArticles, RecentChangelog, RecentComments, TagCloud};
use yii\base\DynamicModel;
use yii\bootstrap4\{ActiveForm, Html};
use yii\caching\DbDependency;

$isHome = Yii::$app->controller->id === 'site' && Yii::$app->controller->action->id === 'index';
$search = new DynamicModel(['search']);
$search->addRule('search', 'required');
$search->addRule('search', 'string', ['max' => 25]);
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
		'header' => 'Changelog',
	]);

	echo Item::widget([
		'body' => Feed::widget(['name' => 'ScienceDaily']),
		'header' => 'Science News',
	]);
$this->endBlock();

$this->beginContent('@app/views/layouts/main.php');
echo Html::beginTag('div', ['class' => 'row']);
	echo Html::tag('div', $content, ['class' => $isHome ? 'col-12 col-md-8 col-lg-6' : 'col-12 col-md-9']);
	if ($isHome) {
			echo Html::tag('aside', $this->blocks['widgets'], ['class' => 'col-3 d-none d-lg-block']);
	}
	echo Html::beginTag('aside', ['class' => 'col-4 col-lg-3 d-none d-md-block']);
		$form = ActiveForm::begin(['action' => ['articles/index', 'action' => 'search'], 'id' => 'search', 'method' => 'get', 'options' => ['role' => 'search']]);
		echo $form->field($search, 'search', [
				'options' => ['class' => 'form-group mb-2'],
				'template' => '<div class="input-group input-group-sm">{input}'.Html::tag('div', Html::submitButton(Icon::show('search'), ['class' => 'btn btn-outline-primary']), ['class' => 'input-group-append'])."</div>",
			])
			->label(false)
			->input('search', ['class' => 'form-control', 'name' => 'q', 'placeholder' => 'Search Articles…', 'value' => Yii::$app->request->get('q')]);
		ActiveForm::end();

		if ($this->beginCache('articlewidgets', ['dependency' => $dependency, 'duration' => 0])) {
			echo Item::widget([
				'body' => RecentArticles::widget(),
				'header' => 'Latest Updates',
				'options' => ['id' => 'latestArticles'],
			]);

			echo Item::widget([
				'body' => RecentComments::widget(),
				'header' => 'Latest Comments',
				'options' => ['id' => 'latestComments'],
			]);

			echo Item::widget([
				'body' => TagCloud::widget(),
				'header' => 'Tag Cloud',
				'options' => ['id' => 'tags'],
			]);

			$this->endCache();
		}

		if (!$isHome) {
					echo $this->blocks['widgets'];
		}
	echo Html::endTag('aside');
echo Html::endTag('div');
$this->endContent();
