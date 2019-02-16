<?php
use app\models\articles\{Articles, Tags};
use app\models\feed\Sitemap;
use yii\helpers\ArrayHelper;

$doc = Sitemap::beginDoc();

$articles = Articles::find()->orderBy('created')->with('comments')->all();
Sitemap::lineItem($doc, ['articles/index'], ['age' => end($articles)->updated, 'priority' => 0.8, 'locale' => true]);

foreach ($articles as $article) :
	$lastUpdate = $article['updated'];
	foreach ($article['comments'] as $comment) :
			$lastUpdate = max($lastUpdate, $comment['created']);
	endforeach;
	Sitemap::lineItem($doc, ['articles/article', 'id' => $article->id, 'title' => $article->url], ['age' => $lastUpdate, 'locale' => true]);
	if ($article['pdf']) :
			Sitemap::lineItem($doc, ['articles/pdf', 'id' => $article->id, 'title' => $article->url], ['age' => $lastUpdate]);
	endif;
endforeach;
unset($articles);

$tags = Tags::findTagWeights();
$weight = ArrayHelper::getColumn($tags, 'weight');
foreach ($tags as $tag => $value) :
	$lastUpdate = Tags::lastUpdate($tag);
	Sitemap::lineItem($doc, ['/articles/tag', 'q' => $tag], ['age' => $lastUpdate, 'priority' =>  $value['weight'] / max($weight) - 0.2, 'locale' => true]);
endforeach;
unset($tags);

echo Sitemap::endDoc($doc);
