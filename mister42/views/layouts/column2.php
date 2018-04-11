<?php
use app\models\articles\{Articles, Comments};
use app\models\site\Changelog;
use app\widgets\{Feed, Item, RecentArticles, RecentChangelog, RecentComments, TagCloud};
use yii\base\DynamicModel;
use yii\bootstrap\{ActiveForm, Html};
use yii\caching\DbDependency;

$this->beginContent('@app/views/layouts/main.php');

$search = new DynamicModel(['search_term']);
$search->addRule('search_term', 'required');
$search->addRule('search_term', 'string', ['max' => 25]);
$dependency = [
	'class' => DbDependency::class,
	'reusable' => true,
	'sql' => 'SELECT GREATEST(
		IFNULL((SELECT MAX(updated) FROM '.Articles::tableName().' WHERE `active` = '.Articles::STATUS_ACTIVE.'), 1),
		IFNULL((SELECT MAX(created) FROM '.Comments::tableName().' WHERE `active` = '.Comments::STATUS_ACTIVE.'), 1),
		IFNULL((SELECT MAX(time) FROM '.Changelog::tableName().'), 1)
	)',
];

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::tag('div', $content, ['class' => 'col-xs-12 col-sm-9']);
	echo Html::beginTag('aside', ['class' => 'hidden-xs col-sm-3']);
		$form = ActiveForm::begin(['action' => ['articles/index', 'action' => 'search'], 'method' => 'get', 'options' => ['role' => 'search']]);
		echo $form->field($search, 'search_term', [
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

			echo Item::widget([
				'body' => RecentChangelog::widget(),
				'header' => Html::tag('h4', 'Changelog'),
				'options' => ['id' => 'changelog'],
			]);

			$this->endCache();
		}

		echo Item::widget([
			'body' => Feed::widget(['name' => 'ScienceDaily']),
			'header' => Html::tag('h4', 'Science News'),
		]);
	echo Html::endTag('aside');
echo Html::endTag('div');
$this->endContent();
