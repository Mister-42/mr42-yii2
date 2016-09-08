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
}
