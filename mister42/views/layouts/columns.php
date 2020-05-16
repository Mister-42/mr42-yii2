<?php

use mister42\models\ActiveForm;
use mister42\models\articles\Articles;
use mister42\models\articles\ArticlesComments;
use mister42\models\articles\Search;
use mister42\models\feed\Feed;
use mister42\widgets\Feed as FeedWidget;
use mister42\widgets\Item;
use mister42\widgets\RecentArticles;
use mister42\widgets\RecentComments;
use mister42\widgets\TagCloud;
use yii\bootstrap4\Html;
use yii\caching\TagDependency;
use yii\helpers\Inflector;

$isHome = Yii::$app->requestedRoute === 'site/index';
$dependency = [
    'class' => TagDependency::class,
    'tags' => ['articles' => Articles::getLastModified(), 'comments' => ArticlesComments::getLastModified()],
    'reusable' => true,
];

$this->beginContent('@app/views/layouts/main.php');
echo Html::beginTag('div', ['class' => 'row']);
    echo Html::tag('div', $content, ['class' => $isHome ? 'col-12 col-md-8 col-lg-6' : 'col-12 col-md-9']);

    echo Html::beginTag('div', ['class' => $isHome ? 'col-4 col-lg-6 d-none d-md-block' : 'col-3 d-none d-md-block']);
        echo Html::beginTag('div', ['class' => 'row']);
            echo Html::beginTag('aside', ['class' => 'col-12 d-none d-md-block']);
                $form = ActiveForm::begin(['action' => ['articles/search'], 'method' => 'get', 'options' => ['role' => 'search']]);
                    echo $form->field(new Search(), 'keyword', [
                        'options' => ['class' => 'form-group mb-2'],
                        'template' => '<div class="input-group input-group-sm">{input}' . Html::tag('div', Html::submitButton(Yii::$app->icon->name('search')->title(Yii::t('mr42', 'Search Articles…')), ['class' => 'btn btn-outline-info']), ['class' => 'input-group-append']) . '</div>',
                    ])->input('search', ['class' => 'form-control', 'name' => 'q', 'placeholder' => Yii::t('mr42', 'Search Articles…'), 'value' => Yii::$app->request->get('q')]);
                ActiveForm::end();
            echo Html::endTag('aside');
        echo Html::endTag('div');

        echo Html::beginTag('div', ['class' => 'row']);
            if ($isHome) {
                echo Html::beginTag('aside', ['class' => 'col-6 d-none d-lg-block']);
                foreach (['science', 'tech'] as $category) {
                    $feed = Feed::find()->where(['type' => $category, 'language' => Yii::$app->language]);
                    if ($feed->count() === '0') {
                        $feed = Feed::find()->where(['type' => $category, 'language' => 'en']);
                    }

                    $feedData = $feed->one();
                    echo Item::widget([
                        'body' => FeedWidget::widget(['name' => $feedData->name, 'tooltip' => true]),
                        'header' => Yii::$app->icon->name($feedData->icon)->class('mr-1') . $feedData->title,
                    ]);
                }
                echo Html::endTag('aside');
            }

            echo Html::beginTag('aside', ['class' => $isHome ? 'col-12 col-lg-6 d-none d-md-block' : 'col-12 d-none d-md-block']);
                if ($this->beginCache('articlewidgets', ['dependency' => $dependency, 'duration' => 0, 'enabled' => !YII_DEBUG, 'variations' => [Yii::$app->language]])) {
                    $widgets = [
                        Yii::t('mr42', 'Recent Articles') => ['class' => RecentArticles::widget(), 'icon' => 'newspaper'],
                        Yii::t('mr42', 'Recent Comments') => ['class' => RecentComments::widget(), 'icon' => 'comments'],
                        Yii::t('mr42', 'Tag Cloud') => ['class' => TagCloud::widget(), 'icon' => 'tags'],
                    ];

                    foreach ($widgets as $title => $val) {
                        echo Item::widget([
                            'body' => $val['class'],
                            'header' => Yii::$app->icon->name($val['icon'])->class('mr-1') . $title,
                            'options' => ['id' => Inflector::slug($title)],
                        ]);
                    }

                    $this->endCache();
                }

                if ($isHome) {
                    echo Item::widget([
                        'body' => FeedWidget::widget(['limit' => 5, 'name' => 'Mr42Commits']),
                        'header' => Yii::$app->icon->name('github', 'brands')->class('mr-1') . Yii::t('mr42', 'Changelog'),
                    ]);
                }
            echo Html::endTag('aside');
        echo Html::endTag('div');

    echo Html::endTag('div');
echo Html::endTag('div');
$this->endContent();
