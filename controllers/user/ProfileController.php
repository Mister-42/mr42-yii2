<?php
namespace app\controllers\user;
use dektrium\user\controllers\ProfileController as BaseProfileController;
use Yii;
use yii\filters\HttpCache;
use yii\helpers\ArrayHelper;
use yii\web\{MethodNotAllowedHttpException, NotFoundHttpException};

class ProfileController extends BaseProfileController
{
	public function behaviors()
	{
		$behaviors = parent::behaviors();
		$behaviors['access']['rules'][] = ['allow' => true, 'actions' => ['recenttracks']];

		$newBehaviors = [
			[
				'class' => HttpCache::className(),
				'etagSeed' => function ($action, $params) {
					return serialize([YII_DEBUG, Yii::$app->user->id, Yii::$app->request->get('username')]);
				},
				'lastModified' => function ($action, $params) {
					$user = $this->finder->findUserByUsername(Yii::$app->request->get('username'));
					$profile = $this->finder->findProfileById($user->id);
					return $profile->user->updated_at;
				},
				'only' => ['show'],
			],
		];

		return ArrayHelper::merge($behaviors, $newBehaviors);
	}

	public function actionShow($username)
	{
		$user = $this->finder->findUserByUsername($username);
		if ($user === null)
			throw new NotFoundHttpException('Profile not found.');

		$profile = $this->finder->findProfileById($user->id);
		if ($profile === null)
			throw new NotFoundHttpException('Profile not found.');

		if ($profile->lastfm)
			$this->layout = '@app/views/layouts/recenttracks.php';			

		return $this->render('show', [
			'profile' => $profile,
		]);
	}

	public function actionRecenttracks($username)
	{
		$user = $this->finder->findUserByUsername($username);
		if ($user === null)
			throw new NotFoundHttpException('Profile not found.');		

		if (!Yii::$app->request->isAjax)
			throw new MethodNotAllowedHttpException('Method Not Allowed.');

		return $this->renderAjax('@app/views/lyrics/recentTracks', [
			'userid' => $user->id,
		]);
	}
}
