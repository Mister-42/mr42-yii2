<?php
namespace app\controllers;
use Yii;
use app\models\MenuItems;
use app\models\lyrics\Lyrics1Artists;
use app\models\lyrics\Lyrics2Albums;
use app\models\post\Post;
use app\models\post\Tags;
use yii\base\Object;
use yii\captcha\CaptchaAction;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\HttpCache;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class TechController extends Controller
{
	public function actions()
	{
		return [
			'captcha' => [
				'class' => CaptchaAction::className(),
				'backColor' => 0xffffff,
				'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
				'foreColor' => 0x003e67,
				'transparent' => true,
			],
		];
	}

	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['playground'],
				'rules' => [
					[
						'actions' => ['playground'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
				'denyCallback' => function ($rule, $action) {
					throw new NotFoundHttpException('Page not found.');
				}
			],
			[
				'class' => HttpCache::className(),
				'only' => ['faviconico'],
				'lastModified' => function (Object $action, $params) {
					return filemtime(Yii::getAlias('@assetsPath/images/'.Yii::$app->params['favicon']));
				},
			],
			[
				'class' => HttpCache::className(),
				'only' => ['robotstxt'],
				'lastModified' => function (Object $action, $params) {
					return filemtime(Yii::getAlias('@app/views/'.$action->controller->id.'/'.$action->id.'.php'));
				},
			],
			[
				'class' => HttpCache::className(),
				'only' => ['rss'],
				'lastModified' => function (Object $action, $params) {
					$q = new Query();
					return $q->from(Post::tableName())->max('updated');
				},
			],
		];
	}

	public function actionFaviconico()
	{
		Yii::$app->response->sendFile(Yii::getAlias('@assetsPath/images/'.Yii::$app->params['favicon']), 'favicon.ico', ['inline' => true]);
	}

	public function actionOffline()
	{
		$this->layout = '@app/views/layouts/offline.php';
		Yii::$app->response->statusCode = 503;
		Yii::$app->response->headers->add('Retry-After', 900);
		return $this->render('offline');
	}

	public function actionPlayground()
	{
		return $this->render('playground');
	}

	public function actionRobotstxt()
	{
		Yii::$app->response->format = Response::FORMAT_RAW;
		Yii::$app->response->headers->add('Content-Type', 'text/plain');
		return $this->renderPartial('robotstxt');
	}

	public function actionRss() {
		Yii::$app->response->format = Response::FORMAT_RAW;
		Yii::$app->response->headers->add('Content-Type', 'application/rss+xml');

		$posts = Post::find()
			->orderBy('created DESC')
			->with('user')
			->limit(5)
			->all();
			
		return $this->renderPartial('rss', [
			'posts' => $posts,
		]);
	}

	public function actionSitemapxml()
	{
		Yii::$app->response->format = Response::FORMAT_RAW;
		Yii::$app->response->headers->add('Content-Type', 'application/xml');

		$pages = MenuItems::urlList(); sort($pages);

		$posts = Post::find()
			->orderBy('created')
			->with('comments')
			->all();

		$tags = Tags::findTagWeights(-1);

		$artists = Lyrics1Artists::artistsList();

		$albums = Lyrics2Albums::albumsList();

		return $this->renderPartial('sitemapxml', [
			'pages' => $pages,
			'posts' => $posts,
			'tags' => $tags,
			'artists' => $artists,
			'albums' => $albums,
		]);
	}
}
