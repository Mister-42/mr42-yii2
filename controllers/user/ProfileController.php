<?php
namespace app\controllers\user;
use Yii;
use app\models\user\RecentTracks;
use Da\User\Query\{ProfileQuery, UserQuery};
use yii\base\Module;
use yii\filters\HttpCache;
use yii\helpers\ArrayHelper;
use yii\web\{MethodNotAllowedHttpException, NotFoundHttpException};

class ProfileController extends \Da\User\Controller\ProfileController {
	protected $userQuery;

	public function __construct($id, Module $module, ProfileQuery $profileQuery, UserQuery $userQuery, array $config = []) {
		$this->userQuery = $userQuery;
		parent::__construct($id, $module, $profileQuery, $config);
	}

	public function behaviors() {
		$behaviors = parent::behaviors();
		$behaviors['access']['rules'][] = ['allow' => true, 'actions' => ['recenttracks']];

		return ArrayHelper::merge($behaviors, [
			[
				'class' => HttpCache::className(),
				'etagSeed' => function ($action, $params) {
					return serialize([YII_DEBUG, Yii::$app->user->id, Yii::$app->request->get('username')]);
				},
				'lastModified' => function ($action, $params) {
					$user = $this->userQuery->whereUsername(Yii::$app->request->get('username'))->one();
					$profile = $this->profileQuery->whereUserId($user->id)->one();
					return $profile->user->updated_at;
				},
				'only' => ['show'],
			],
		]);
	}

	public function actionShow($username) {
		$user = $this->userQuery->whereUsername($username)->one();
		if (!$user)
			throw new NotFoundHttpException('User not found.');

		$profile = $this->profileQuery->whereUserId($user->id)->one();
		if ($profile->lastfm)
			$this->layout = '@app/views/layouts/recenttracks.php';

		return parent::actionShow($user->id);
	}

	public function actionRecenttracks($username) {
		$user = $this->userQuery->whereUsername($username)->one();
		if (!$user)
			throw new NotFoundHttpException('Profile not found.');

		if (!Yii::$app->request->isAjax)
			throw new MethodNotAllowedHttpException('Method Not Allowed.');

		return RecentTracks::display($user->id);
	}
}
