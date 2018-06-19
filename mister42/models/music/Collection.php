<?php
namespace app\models\music;
use app\models\{Image, Webrequest};
use yii\console\Controller;
use yii\helpers\ArrayHelper;

class Collection extends \yii\db\ActiveRecord {
	public static function tableName(): string {
		return '{{%discogs_collection}}';
	}

	public function saveCollection(int $user, array $data) {
		foreach ($data as $item) :
			$addItem = new Collection();
			$addItem->id = (int) ArrayHelper::getValue($item, 'basic_information.id');
			$addItem->user_id = $user;
			$addItem->artist = ArrayHelper::getValue($item, 'basic_information.artists.0.name');
			$addItem->year = (int)ArrayHelper::getValue($item, 'basic_information.year');
			$addItem->title = ArrayHelper::getValue($item, 'basic_information.title');
			$addItem->image = null;
			if ($image = ArrayHelper::getValue($item, 'basic_information.cover_image')) :
				$img = Webrequest::getUrl($image, '');
				$addItem->image = Image::resize($img->content, 250);
			endif;
			$addItem->save();
		endforeach;
	}
}
