<?php
namespace app\models\post;
use app\models\post\Post;
use yii\helpers\StringHelper;

class Tags
{
	public static function findTagWeights($limit = 20) {
		$tags = self::getTags();

		$total = array_sum($tags);
		foreach($tags as $key => $value)
			$list[$key] = 8 + (int)(16 * $value / ($total + 10));

		array_slice($list, 0, $limit);
		ksort($list, SORT_NATURAL | SORT_FLAG_CASE);
		return ($total === 0) ? [] : $list;
	}

	public static function lastUpdate($tag) {
		$lastUpdate = Post::find()
			->select(['updated' => 'max(updated)'])
			->where(['like', 'tags', $tag])
			->one();

		return $lastUpdate['updated'];
	}

	private static function getTags() {
		foreach (Post::find()->select('tags')->all() as $tag) :
			foreach (StringHelper::explode($tag->tags) as $item)
				$list[$item] = $list[$item] ? $list[$item] + 1 : 1;
		endforeach;

		return $list;
	}
}
