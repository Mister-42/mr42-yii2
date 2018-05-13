<?php
namespace app\models\articles;
use yii\helpers\StringHelper;

class Tags extends BaseArticles {
	public static function findTagWeights(): array {
		if (empty($tags = self::getTags())) {
					return [];
		}
		foreach ($tags as $key => $value) :
			$list[$key]['count'] = $value;
			$list[$key]['weight'] = 8 + (int) (16 * $value / (array_sum($tags) + 10));
		endforeach;
		ksort($list, SORT_NATURAL | SORT_FLAG_CASE);
		return $list;
	}

	public static function lastUpdate(string $tag): int {
		$lastUpdate = self::find()
			->select(['updated' => 'max(updated)'])
			->where(['like', 'tags', $tag])
			->one();
		return $lastUpdate['updated'];
	}

	private function getTags(): array {
		foreach (self::find()->select('tags')->all() as $tag) :
			foreach (StringHelper::explode($tag->tags) as $item) {
							$list[$item]++;
			}
		endforeach;
		return $list;
	}
}
