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
    private const ALBUM_IMAGE_DIMENSIONS = 250;

    private string $artist;
    private string $date;
    private int $id;
    private ?string $image;
    private ?string $image_color;
    private ?string $image_override;
    private string $status;
    private string $title;
    private string $updated;
    private int $user_id;
    private int $year;

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
        return is_string($data) ? strtotime($data) : 0;
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
        foreach ($data as $discogs) {
            $item = self::findOne(['id' => (int) ArrayHelper::getValue($discogs, 'basic_information.id'), 'user_id' => $user, 'status' => $status]) ?? new self();

            $image = ArrayHelper::getValue($discogs, 'basic_information.cover_image');
            if (isset($item->image_override) && Image::isValid($item->image_override)) {
                $item->image = null;
                [$width, $height] = getimagesizefromstring($item->image_override);
                if (min($width, $height) > self::ALBUM_IMAGE_DIMENSIONS) {
                    $item->image_override = Image::resize($item->image_override, self::ALBUM_IMAGE_DIMENSIONS);
                    $item->image_color = null;
                }
            } elseif ($image && !isset($item->image)) {
                $client = new Client('');
                $img = $client->getFile($image);
                $item->image = ($img->isOk && Image::isValid($img->content)) ? Image::resize($img->content, self::ALBUM_IMAGE_DIMENSIONS) : null;
            }

            $item->id = ArrayHelper::getValue($discogs, 'basic_information.id');
            $item->user_id = $user;
            $item->artist = trim(preg_replace('/\([0-9]+\)/', '', ArrayHelper::getValue($discogs, 'basic_information.artists.0.name')));
            $item->year = ArrayHelper::getValue($discogs, 'basic_information.year');
            $item->title = ArrayHelper::getValue($discogs, 'basic_information.title');
            $item->status = $status;
            $item->created = Yii::$app->formatter->asDatetime(ArrayHelper::getValue($discogs, 'date_added'), 'yyyy-MM-dd HH:mm:ss');

            if (!isset($item->image_color) && (isset($item->image) || isset($item->image_override))) {
                $item->image_color = Image::getAverageImageColor($item->image ?? $item->image_override);
            }

            $item->save();
            $id[] = $item->id;
        }

        return $id;
    }

    public static function tableName(): string
    {
        return '{{%discogs_collection}}';
    }
}
