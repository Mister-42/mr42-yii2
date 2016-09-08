<?php
namespace app\controllers;
use Yii;
use app\models\General;
use app\models\Pdf;
use app\models\post\Comment;
use app\models\post\Post;
use app\models\post\Tags;
use dektrium\user\models\User;
use yii\bootstrap\Alert;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

class PostController extends Controller
{
	public $layout = '@app/views/layouts/column2.php';

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
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['create', 'update', 'delete', 'commentstatus'],
				'rules' => [
					[
						'actions' => ['create', 'update', 'delete', 'commentstatus'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'delete' => ['post'],
				],
			],
		];
	}

	public function actionIndex($id = '', $title = '', $page = '', $action = '', $tag = '', $q = '')
	{
		if (isset($id) && !empty($id)) {
			$model = $this->findModel($id, ['comments']);
			$comment = new Comment;

			if ($comment->load(Yii::$app->request->post())) {
				if ($model->addComment($comment)) {
					if (!Yii::$app->user->isGuest) {
						$comment->name = Yii::$app->user->identity->username;
						$comment->email = Yii::$app->user->identity->email;
					}
					$comment->sendCommentMail($model, $comment);
					return Alert::widget(['options' => ['class' => 'alert-success'], 'body' => 'Your comment has been saved. It will not be visible until approved by an administrator.', 'closeButton' => false]);
				}
				return Alert::widget(['options' => ['class' => 'alert-danger'], 'body' => 'Something went wrong, Your comment has not been saved.', 'closeButton' => false]);
			}

			if (empty($title) || $title != $model->title)
				$this->redirect(['index', 'id' => $model->id, 'title' => $model->title], 301)->send();

			Yii::$app->view->registerLinkTag(['rel' => 'alternate', 'href' => Url::to(['pdf', 'id' => $model->id, 'title' => $model->title], true), 'type' => 'application/pdf', 'title' => 'PDF']);
			return $this->render('view', [
				'model' => $model,
				'comment' => $comment
			]);
		}

		$query = Post::find()->orderBy('id DESC');
		if ($action === "tag" && !empty($tag)) {
			$query->where(['like', 'tags', '%'.$tag, false]);
		} elseif ($action === "search" && !empty($q)) {
			Yii::$app->view->registerMetaTag(['name' => 'robots', 'content' => 'noindex']);
			$query->where(['or like', 'title', $q]);
			$query->orWhere(['like', 'content', $q]);
		}

		return $this->render('index', [
			'dataProvider' => new ActiveDataProvider([
				'query' => $query,
				'pagination' => [
					'defaultPageSize' => 1,
				],
			]),
			'action' => $action,
			'tag' => $tag,
			'q' => $q,
		]);
	}

	public function actionPdf($id = '', $title = '')
	{
		if (isset($id) && !empty($id)) {
			$model = $this->findModel($id);

			if (empty($title) || $title != $model->title)
				$this->redirect(['pdf', 'id' => $model->id, 'title' => $model->title], 301)->send();

			$user = new User();
			$model->content = General::cleanInput($model->content, 'gfm', true);
			$profile = $user->finder->findProfileById($model->user->id);
			$name = (empty($profile->name) ? Html::encode($model->user->username) : Html::encode($profile->name));
			$tags = (count(Tags::string2array($model->tags)) > 1) ? 'tags' : 'tag';

			$pdf = new Pdf();
			$fileName = $pdf->create(
				'@runtime/pdf/posts/'.sprintf('%05d', $model->id),
				$this->renderPartial('pdf', ['model' => $model]),
				$model->updated,
				[
					'author' => $name,
					'created' => $model->created,
					'footer' => $tags.': '.$model->tags.'|Author: '.$name.'|Page {PAGENO} of {nb}',
					'header' => Html::a(Yii::$app->name, Url::to(Yii::$app->homeUrl, true)).'|'.Html::a($model->title, Url::to(['index', 'id' => $model->id], true)).'|' . date('D, j M Y', $model->updated),
					'keywords' => $model->tags,
					'subject' => $model->title,
					'title' => implode(' ∷ ', [$model->title, Yii::$app->name]),
				]		
			);
			Yii::$app->response->sendFile($fileName, Yii::$app->name.' - '.$model->id.' - '.$model->title.'.pdf');
		}
	}

	public function actionCreate()
	{
		$this->layout = '@app/views/layouts/main.php';

		$model = new Post;
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['index', 'id' => $model->id, 'title' => $model->title]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	public function actionUpdate($id)
	{
		$this->layout = '@app/views/layouts/main.php';

		$model = $this->findModel($id);
		if (!$model->belongsToViewer()) {
			throw new UnauthorizedHttpException('You do not have permission to edit this post.');
		}

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['index', 'id' => $model->id, 'title' => $model->title]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	public function actionDelete($id)
	{
		$model = $this->findModel($id);
		if (!$model->belongsToViewer()) {
			throw new UnauthorizedHttpException('You do not have permission to edit this post.');
		}
		$model->delete();
		return $this->redirect(['index']);
	}

	public function actionCommentstatus($action, $id)
	{
		$comment = Comment::findOne($id);
		$post = $this->findModel($comment->parent);

		if (!$post->belongsToViewer())
			throw new UnauthorizedHttpException('You do not have permission to edit this comment.');

		$comment->active = ($comment->active) ? Comment::STATUS_INACTIVE : Comment::STATUS_ACTIVE;
		if ($action == "toggleapproval") {
			if (!Yii::$app->request->isAjax)
				throw new MethodNotAllowedHttpException('Method Not Allowed. This url can only handle the following request methods: AJAX.');
			$comment->update();
			return $comment->showApprovalButton();
		} elseif ($action == "delete") {
			$comment->delete();
			return $this->redirect(['index', 'id' => $post->id, 'title' => $post->title, '#' => 'comments']);
		}

		return false;
	}

/*
	public function actionSuggestTags() {
		if(isset($_GET['q']) && ($keyword=trim($_GET['q']))!=='') {
			$tags=Tags::suggestTags($keyword);
			if($tags!==array())
				echo implode("\n",$tags);
		}
	}
*/

	protected function findComment($id)
	{
		$query = Comment::find()
				->where(['id' => $id])
				->andWhere(Yii::$app->user->isGuest ? ['`active`' => Post::STATUS_ACTIVE] : ['or', ['`active`' => [Post::STATUS_INACTIVE, Post::STATUS_ACTIVE]]])
				->with('user')
		;

		$model = $query->one();

		if ($model === null)
			throw new NotFoundHttpException('Page not found.');

		$post = $this->findModel($id);
		if (!$post->belongsToViewer()) {
			throw new UnauthorizedHttpException('You do not have permission to edit this comment.');
		}

		return $model;
	}

	protected function findModel($id, $withList = [])
	{
		$query = Post::find()
				->where(['id' => $id])
				->andWhere(Yii::$app->user->isGuest ? ['active' => Post::STATUS_ACTIVE] : ['or', ['active' => [Post::STATUS_INACTIVE, Post::STATUS_ACTIVE]]])
				->with('user');

		foreach ($withList as $with) {
			$query->with($with);
		}

		$model = $query->one();

		if ($model === null)
			throw new NotFoundHttpException('Page not found.');

		return $model;
	}
}
