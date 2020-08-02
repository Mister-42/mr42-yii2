<?php

use mister42\models\articles\Articles;
use mister42\models\articles\Tags;
use mr42\models\Sitemap;
use yii\helpers\ArrayHelper;

$doc = Sitemap::beginDoc();

$articles = Articles::find()->orderBy(['created' => SORT_ASC])->with('comments')->all();
Sitemap::lineItem($doc, ['articles/index'], ['age' => end($articles)->updated, 'priority' => 0.8, 'locale' => true]);

foreach ($articles as $article) {
    $lastModified = Yii::$app->formatter->asTimestamp($article['updated']);
    foreach ($article['comments'] as $comment) {
        $lastModified = max($lastModified, Yii::$app->formatter->asTimestamp($comment['created']));
    }

    Sitemap::lineItem($doc, ['articles/article', 'id' => $article->id, 'title' => $article->url], ['age' => $lastModified, 'locale' => true]);
    if ($article['pdf']) {
        $pdfUrl = Yii::$app->mr42->createUrl(['articles/pdf', 'id' => $article->id, 'title' => $article->url], ['age' => $lastModified]);
        Sitemap::lineItem($doc, $pdfUrl, ['age' => $lastModified]);
    }
}
unset($articles);

$tags = Tags::findTagWeights();
$weight = ArrayHelper::getColumn($tags, 'weight');
foreach ($tags as $tag => $value) {
    $lastModified = Tags::getLastUpdate($tag);
    Sitemap::lineItem($doc, ['articles/tag', 'tag' => $tag], ['age' => $lastModified, 'priority' => $value['weight'] / max($weight) - 0.2, 'locale' => true]);
}
unset($tags);

echo Sitemap::endDoc($doc);
