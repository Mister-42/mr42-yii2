<?php

namespace app\models\articles;

use yii\helpers\StringHelper;

class Tags extends Articles {
	public static function findTagWeights(): array {
		if (empty($tags = self::getTags())) {
			return [];
		}
		foreach ($tags as $key => $value) {
			$list[$key]['count'] = $value;
			$list[$key]['weight'] = 8 + (int) (16 * $value / (array_sum($tags) + 10));
		}
		ksort($list, SORT_NATURAL | SORT_FLAG_CASE);
		return $list;
	}

	public static function getLastUpdate(string $tag): int {
		$data = self::find()
			->where(['like', 'tags', $tag])
			->max('updated');
		return $data;
	}

	private static function getTags(): array {
		$list = [];
		foreach (self::find()->select('tags')->all() as $tag) {
			foreach (StringHelper::explode($tag->tags) as $item) {
				isset($list[$item]) ? $list[$item]++ : $list[$item] = 1;
			}
		}
		return $list;
	}
}
