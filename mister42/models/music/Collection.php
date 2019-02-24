<?php
namespace app\models\music;
use app\models\{Image, Webrequest};
use yii\helpers\ArrayHelper;

class Collection extends \yii\db\ActiveRecord {
	public static function tableName(): string {
		return '{{%discogs_collection}}';
	}

	public function saveCollection(int $user, array $data, string $status): array {
		foreach ($data as $item) :
			$id[] = (int) ArrayHelper::getValue($item, 'basic_information.id');

			if (!$collectionItem = Collection::findOne(['id' => (int) ArrayHelper::getValue($item, 'basic_information.id'), 'user_id' => $user])) :
				$collectionItem = new Collection();
				$collectionItem->image = null;
				if ($image = ArrayHelper::getValue($item, 'basic_information.cover_image')) :
					$img = Webrequest::getUrl($image, '');
					if ($img = Image::resize($img->content, 250))
						$collectionItem->image = $img;
				endif;
			endif;

			$collectionItem->id = (int) ArrayHelper::getValue($item, 'basic_information.id');
			$collectionItem->user_id = $user;
			$collectionItem->artist = trim(preg_replace('/\([0-9]+\)/', '', ArrayHelper::getValue($item, 'basic_information.artists.0.name')));
			$collectionItem->year = (int) ArrayHelper::getValue($item, 'basic_information.year');
			$collectionItem->title = ArrayHelper::getValue($item, 'basic_information.title');
			$collectionItem->status = $status;
			$collectionItem->save();
		endforeach;

		return $id ?? [];
	}

	public static function getLastModified(): int {
		$data = self::find()
			->max('updated');
		return strtotime($data);
	}

	public static function getEntryLastModified(int $id): int {
		$data = self::findOne(['id' => $id]);
		return strtotime($data->updated);
	}
}
