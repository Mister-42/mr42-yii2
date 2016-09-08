<?php
namespace app\models\post;
use app\models\post\Post;

class Tags
{
	public static function findTagWeights($limit = 20) {
		$tags = self::getTags();

		$total = array_sum($tags);
		foreach($tags as $key => $value)
			$list[$key] = 8 + (int)(16 * $value / ($total + 10));

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

	public function suggestTags($keyword, $limit = 20) {
		$tags = self::find()
#			'condition' => 'name LIKE :keyword',
			->orderBy('frequency DESC, name')
			->limit($limit)
#			'params' => array(
#				':keyword'=>'%'.strtr($keyword, ['%'=>'\%', '_'=>'\_', '\\'=>'\\\\']).'%',
			>all();

		$names = [];
		foreach($tags as $tag)
			$names[] = $tag->name;
		return $names;
	}

	public static function array2string($tags) {
		return implode(', ', $tags);
	}

	public static function string2array($tags) {
		return preg_split('/\s*,\s*/', trim($tags), -1, PREG_SPLIT_NO_EMPTY);
	}

	private static function getTags() {
		foreach (Post::find()->select('tags')->all() as $tag) {
			foreach (self::string2array($tag->tags) as $item) {
				$list[$item] = (isset($list[$item])) ? $list[$item] + 1 : 1;
			}
		}

		return $list;
	}
}
