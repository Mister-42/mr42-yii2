<?php

namespace mister42\models\music;

use mister42\models\Apirequest;
use mister42\models\Image;
use mister42\models\user\Profile;
use thoulah\httpclient\Client;
use Yii;
use yii\helpers\ArrayHelper;

class Collection extends \yii\db\ActiveRecord
{
    public static function getDiscogsUrl(string $action, Profile $profile) : ?string
    {
        if ($action === 'collection') {
            $response = Apirequest::getDiscogs("users/{$profile->discogs}/collection/folders?" . http_build_query(['token' => $profile->discogs_token]));
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

    public function rules(): array
    {
        return [
            [['id', 'user_id', 'status'], 'unique', 'targetAttribute' => ['id', 'user_id', 'status']],
        ];
    }

    public function saveCollection(int $user, array $data, string $status): array
    {
        $id = [];
        foreach ($data as $item) {
            $id[] = (int) ArrayHelper::getValue($item, 'basic_information.id');

            if (!$collectionItem = self::findOne(['id' => (int) ArrayHelper::getValue($item, 'basic_information.id'), 'user_id' => $user, 'status' => $status])) {
                $collectionItem = new self();
                $collectionItem->image = null;
                $image = ArrayHelper::getValue($item, 'basic_information.cover_image');
                if ($image) {
                    $client = new Client('');
                    $img = $client->getFile($image);
                    $img = Image::resize($img->content, 250);
                    if ($img) {
                        $collectionItem->image = $img;
                    }
                }
            }

            $collectionItem->id = (int) ArrayHelper::getValue($item, 'basic_information.id');
            $collectionItem->user_id = $user;
            $collectionItem->artist = trim(preg_replace('/\([0-9]+\)/', '', ArrayHelper::getValue($item, 'basic_information.artists.0.name')));
            $collectionItem->year = (int) ArrayHelper::getValue($item, 'basic_information.year');
            $collectionItem->title = ArrayHelper::getValue($item, 'basic_information.title');
            $collectionItem->image_color = null;
            $collectionItem->status = $status;
            $collectionItem->created = Yii::$app->formatter->asDatetime(ArrayHelper::getValue($item, 'date_added'), 'yyyy-MM-dd HH:mm:ss');

            if ($collectionItem->image_color === null) {
                $collectionItem->image_color = Image::getAverageImageColor($collectionItem->image);
            }

            $collectionItem->save();
        }

        return $id;
    }

    public static function tableName(): string
    {
        return '{{%discogs_collection}}';
    }
}
