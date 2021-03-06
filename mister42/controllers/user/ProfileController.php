<?php

namespace mister42\controllers\user;

use Da\User\Query\ProfileQuery;
use Da\User\Query\UserQuery;
use mister42\models\user\RecentTracks;
use Yii;
use yii\base\Module;
use yii\filters\HttpCache;
use yii\helpers\ArrayHelper;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;

class ProfileController extends \Da\User\Controller\ProfileController
{
    protected UserQuery $userQuery;

    public function __construct($id, Module $module, ProfileQuery $profileQuery, UserQuery $userQuery, array $config = [])
    {
        $this->userQuery = $userQuery;
        parent::__construct($id, $module, $profileQuery, $config);
    }

    public function actionRecenttracks($username)
    {
        $user = $this->userQuery->whereUsername($username)->one();
        if (!$user) {
            throw new NotFoundHttpException('Profile not found.');
        }

        if (!Yii::$app->request->isAjax) {
            throw new MethodNotAllowedHttpException('Method Not Allowed.');
        }

        return (new RecentTracks())->display($user->id);
    }

    public function actionShow($username)
    {
        $user = $this->userQuery->whereUsername($username)->one();
        if (!$user) {
            throw new NotFoundHttpException('User not found.');
        }

        $profile = $this->profileQuery->whereUserId($user->id)->one();
        if ($profile->lastfm) {
            $this->layout = '@app/views/layouts/recenttracks.php';
        }

        return parent::actionShow($user->id);
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'][] = ['allow' => true, 'actions' => ['recenttracks']];

        return ArrayHelper::merge($behaviors, [
            [
                'class' => HttpCache::class,
                'enabled' => !YII_DEBUG,
                'etagSeed' => function () {
                    return serialize([Yii::$app->user->id, Yii::$app->request->get('username')]);
                },
                'lastModified' => function () {
                    $user = $this->userQuery->whereUsername(Yii::$app->request->get('username'))->one();
                    $profile = $this->profileQuery->whereUserId($user->id)->one();
                    return $profile->user->updated_at;
                },
                'only' => ['show'],
            ],
        ]);
    }
}
