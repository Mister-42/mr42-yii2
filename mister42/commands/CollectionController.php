<?php

namespace mister42\commands;

use mister42\models\music\Collection;
use mister42\models\user\Profile;
use mister42\models\user\User;
use mister42\models\Webrequest;
use yii\console\ExitCode;
use yii\helpers\ArrayHelper;

/**
 * Handles collection data.
 */
class CollectionController extends \yii\console\Controller
{
    const ALBUM_IMAGE_DIMENSIONS = 1000;

    public $defaultAction = 'update';

    /**
     * Retrieves and stores Discogs data.
     */
    public function actionUpdate(): int
    {
        $discogs = new Collection();
        foreach (User::find()->where(['blocked_at' => null])->all() as $user) {
            $profile = Profile::findOne(['user_id' => $user->id]);
            if (isset($profile->discogs) && isset($profile->discogs_token)) {
                foreach (['collection', 'wishlist'] as $action) {
                    if (!$url = Collection::getDiscogsUrl($action, $profile)) {
                        continue;
                    }
                    $response = Webrequest::getDiscogsApi("{$url}?" . http_build_query(['token' => $profile->discogs_token]));
                    if (!$response->isOK) {
                        continue;
                    }
                    $ids = $discogs->saveCollection($profile->user_id, $response->data[($action === 'collection') ? 'releases' : 'wants'], $action);

                    for ($x = 2; $x < (int) ArrayHelper::getValue($response->data, 'pagination.pages'); $x++) {
                        $response = Webrequest::getDiscogsApi("{$url}?" . http_build_query(['page' => $x, 'token' => $profile->discogs_token]));
                        if (!$response->isOK) {
                            continue;
                        }
                        $subids = $discogs->saveCollection($profile->user_id, $response->data[($action === 'collection') ? 'releases' : 'wants'], $action);
                        $ids = array_merge($ids, $subids);
                    }
                    Collection::deleteAll(['AND', ['user_id' => $profile->user_id], ['NOT IN', 'id', $ids], ['status' => $action]]);
                }
            }
        }

        return ExitCode::OK;
    }
}
