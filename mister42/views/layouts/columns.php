<?php
use app\models\articles\{Articles, Comments};
use app\widgets\{Feed, Item, RecentArticles, RecentChangelog, RecentComments, TagCloud};
use yii\base\DynamicModel;
use yii\bootstrap\{ActiveForm, Html};
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
		'body' => RecentChangelog::widget(),
		'header' => Html::tag('h4', Yii::$app->name . ' Changelog'),
		'options' => ['id' => 'changelog'],
	]);

	echo Item::widget([
		'body' => Feed::widget(['name' => 'ScienceDaily']),
		'header' => Html::tag('h4', 'Science News'),
	]);
$this->endBlock();

$this->beginContent('@app/views/layouts/main.php');
echo Html::beginTag('div', ['class' => 'row']);
	if ($isHome)
		echo Html::tag('aside', $this->blocks['widgets'], ['class' => 'hidden-xs col-sm-3']);
	echo Html::tag('div', $content, ['class' => $isHome ? 'col-xs-12 col-sm-6' : 'col-xs-12 col-sm-9']);
	echo Html::beginTag('aside', ['class' => 'hidden-xs col-sm-3']);
		$form = ActiveForm::begin(['action' => ['articles/index', 'action' => 'search'], 'method' => 'get', 'options' => ['role' => 'search']]);
		echo $form->field($search, 'search', [
				'template' => '<div class="input-group input-group-sm">{input}' . Html::tag('span', Html::submitButton(Html::icon('search'), ['class' => 'btn btn-primary']), ['class' => 'input-group-btn']) . "</div>",
			])
			->label(false)
			->input('search', ['class' => 'form-control', 'name' => 'q', 'placeholder' => 'Search Articles…', 'value' => Yii::$app->request->get('q')]);
		ActiveForm::end();

		if ($this->beginCache('articlewidgets', ['dependency' => $dependency, 'duration' => 0])) {
			echo Item::widget([
				'body' => RecentArticles::widget(),
				'header' => Html::tag('h4', 'Latest Updates'),
				'options' => ['id' => 'latestArticles'],
			]);

			echo Item::widget([
				'body' => RecentComments::widget(),
				'header' => Html::tag('h4', 'Latest Comments'),
				'options' => ['id' => 'latestComments'],
			]);

			echo Item::widget([
				'body' => TagCloud::widget(),
				'header' => Html::tag('h4', 'Tags'),
				'options' => ['id' => 'tags'],
			]);

			$this->endCache();
		}

		if (!$isHome)
			echo $this->blocks['widgets'];
	echo Html::endTag('aside');
echo Html::endTag('div');
$this->endContent();
