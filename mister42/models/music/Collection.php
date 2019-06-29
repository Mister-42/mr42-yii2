<?php

namespace app\models\music;

use app\models\user\Profile;
use app\models\Image;
use app\models\Webrequest;
use Yii;
use yii\helpers\ArrayHelper;

class Collection extends \yii\db\ActiveRecord
{
    public static function getDiscogsUrl(string $action, Profile $profile) : ?string
    {
        if ($action === 'collection') {
            $response = Webrequest::getDiscogsApi("users/{$profile->discogs}/collection/folders?" . http_build_query(['token' => $profile->discogs_token]));
            if (!$response->isOK) {
                return null;
            }
            return "/users/{$profile->discogs}/collection/folders/{$response->data['folders'][1]['id']}/releases";
        }

        return "/users/{$profile->discogs}/wants";
    }

    public static function getEntryLastModified(int $id): int
    {
        $data = self::findOne(['id' => $id]);
        return strtotime($data->created);
    }

    public static function getLastModified(): int
    {
        $data = self::find()
            ->where(['status' => Yii::$app->controller->action->id])
            ->max('created');
        return strtotime($data);
    }

    public function saveCollection(int $user, array $data, string $status): array
    {
        foreach ($data as $item) {
            $id[] = (int) ArrayHelper::getValue($item, 'basic_information.id');

            if (!$collectionItem = self::findOne(['id' => (int) ArrayHelper::getValue($item, 'basic_information.id'), 'user_id' => $user])) {
                $collectionItem = new self();
                $collectionItem->image = null;
                if ($image = ArrayHelper::getValue($item, 'basic_information.cover_image')) {
                    $img = Webrequest::getUrl($image, '')->send();
                    if ($img = Image::resize($img->content, 250)) {
                        $collectionItem->image = $img;
                    }
                }
            }

            $collectionItem->id = (int) ArrayHelper::getValue($item, 'basic_information.id');
            $collectionItem->user_id = $user;
            $collectionItem->artist = trim(preg_replace('/\([0-9]+\)/', '', ArrayHelper::getValue($item, 'basic_information.artists.0.name')));
            $collectionItem->year = (int) ArrayHelper::getValue($item, 'basic_information.year');
            $collectionItem->title = ArrayHelper::getValue($item, 'basic_information.title');
            $collectionItem->status = $status;
            $collectionItem->created = Yii::$app->formatter->asDatetime(ArrayHelper::getValue($item, 'date_added'), 'yyyy-MM-dd HH:mm:ss');
            $collectionItem->save();
        }

        return $id ?? [];
    }
    public static function tableName(): string
    {
        return '{{%discogs_collection}}';
    }
}
