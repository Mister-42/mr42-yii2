<?php
namespace app\controllers;
use Yii;
use app\models\post\Post;
use app\models\site\Contact;
use yii\bootstrap\Alert;
use yii\base\Object;
use yii\filters\HttpCache;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;

class SiteController extends Controller
{
	public function actions()
	{
		return [
			'error' => [
				'class' => ErrorAction::className(),
			],
		];
	}

	public function behaviors()
	{
		return [
			[
				'class' => HttpCache::className(),
				'only' => ['credits'],
				'lastModified' => function (Object $action, $params) {
					return filemtime(Yii::getAlias('@app/views/'.$action->controller->id.'/'.$action->id.'.php'));
				},
			],
		];
	}

	public function actionIndex()
	{
		$this->layout = '@app/views/layouts/column2.php';
		$model = new Post;
		$limit = (isset(Yii::$app->params['recentPosts']) && is_int(Yii::$app->params['recentPosts'])) ? Yii::$app->params['recentPosts'] : 5;

		return  $this->render('index', [
			'model' => $model,
			'posts' => Post::find()
				->orderBy('id desc')
				->limit($limit)
				->all(),
		]);
	}

	public function actionChangelog()
	{
		if (Yii::$app->request->post()) {
			list($algo, $hash) = explode('=', $_SERVER['HTTP_X_HUB_SIGNATURE'], 2);
			if (!hash_equals($hash, hash_hmac($algo, file_get_contents('php://input'), Yii::$app->params['GitHubHook'])))
				throw new UnauthorizedHttpException('Access denied!');

			Yii::$app->response->format = Response::FORMAT_JSON;
			$payload = json_decode(Yii::$app->request->post('payload'));

			if ($_SERVER['X-GitHub-Event'] !== 'push')
				throw new NotFoundHttpException('Action not found.');

			$limit = (isset(Yii::$app->params['feedItemCount']) && is_int(Yii::$app->params['feedItemCount'])) ? Yii::$app->params['feedItemCount'] : 25;
			$c = curl_init();
			curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($c, CURLOPT_URL, str_replace('{/sha}', '', $payload->repository->commits_url));
			curl_setopt($c, CURLOPT_USERAGENT, Yii::$app->name . ' 0.1');
			$file = curl_exec($c);
			curl_close($c);

			if ($file === false)
				throw new ServerErrorHttpException('Unknown error');

			$json = json_decode($file);
			$count = 0;
			Feed::deleteAll(['feed' => $name]);
			foreach($json as $item) {
				$rssItem = new Feed();
				$rssItem->feed = $name;
				$rssItem->title = (string) $item->sha;
				$rssItem->url = (string) $item->html_url;
				$rssItem->description = General::cleanInput($item->commit->message, false);
				$rssItem->time = strtotime($item->commit->committer->date);
				$rssItem->save();

				$count++;
				if ($count === $limit)
					break;
			}

			return ['status' => 'success', 'message' => 'Successfully updated.'];
		}

		return $this->render('changelog');
	}

	public function actionContact()
	{
		$model = new Contact;
		if ($model->load(Yii::$app->request->post()) && $model->contact())
			return Alert::widget(['options' => ['class' => 'alert-success'], 'body' => 'Thank you for contacting us. We will respond to you as soon as possible.', 'closeButton' => false]);

		return $this->render('contact', [
			'model' => $model,
		]);
	}

	public function actionCredits()
	{
		return $this->render('credits');
	}

	public function beforeAction($action)
	{
		if ($action->id == 'changelog') {
			$this->enableCsrfValidation = false;
		}

		return parent::beforeAction($action);
	}
}
