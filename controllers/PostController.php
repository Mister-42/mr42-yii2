<?php
namespace app\controllers;
use Yii;
use app\models\General;
use app\models\post\{Comment, Post};
use yii\bootstrap\Alert;
use yii\data\ActiveDataProvider;
use yii\filters\{AccessControl, VerbFilter};
use yii\helpers\Url;
use yii\web\{Controller,ErrorAction, MethodNotAllowedHttpException, NotFoundHttpException, UnauthorizedHttpException};

class PostController extends Controller {
	public $layout = '@app/views/layouts/column2.php';

	public function actions() {
		return [
			'error' => [
				'class' => ErrorAction::className(),
			],
		];
	}

	public function behaviors() {
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

	public function actionIndex($id = '', $title = '', $page = '', $action = '', $tag = '', $q = '') {
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

			if (empty($title) || $title != $model->url)
				$this->redirect(['index', 'id' => $model->id, 'title' => $model->url], 301)->send();

			Yii::$app->view->registerLinkTag(['rel' => 'alternate', 'href' => Url::to(['pdf', 'id' => $model->id, 'title' => $model->url], true), 'type' => 'application/pdf', 'title' => 'PDF']);
			return $this->render('view', [
				'model' => $model,
				'comment' => $comment
			]);
		}

		$query = Post::find()->orderBy('id DESC');
		if ($action === "tag" && !empty($tag)) {
			$query->where(['like', 'tags', '%'.$tag.'%', false]);
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

	public function actionPdf($id = '', $title = '') {
		if (isset($id) && !empty($id)) {
			$model = $this->findModel($id);

			if (empty($title) || $title != $model->url)
				$this->redirect(['pdf', 'id' => $model->id, 'title' => $model->url], 301)->send();

			$model->content = General::cleanInput($model->content, 'gfm', true);
			$html = $this->renderPartial('pdf', ['model' => $model]);

			$fileName = Post::buildPdf($model, $html);
			Yii::$app->response->sendFile($fileName, Yii::$app->name.' - '.$model->id.' - '.$model->url.'.pdf');
		}
	}

	public function actionCreate() {
		$this->layout = '@app/views/layouts/main.php';

		$model = new Post;
		if ($model->load(Yii::$app->request->post()) && $model->save())
			return $this->redirect(['index', 'id' => $model->id, 'title' => $model->url]);

		return $this->render('create', [
			'model' => $model,
		]);
	}

	public function actionUpdate($id) {
		$this->layout = '@app/views/layouts/main.php';

		$model = $this->findModel($id);
		if (!$model->belongsToViewer())
			throw new UnauthorizedHttpException('You do not have permission to edit this post.');

		if ($model->load(Yii::$app->request->post()) && $model->save())
			return $this->redirect(['index', 'id' => $model->id, 'title' => $model->url]);

		return $this->render('update', [
			'model' => $model,
		]);
	}

	public function actionDelete($id) {
		$model = $this->findModel($id);
		if (!$model->belongsToViewer()) {
			throw new UnauthorizedHttpException('You do not have permission to edit this post.');
		}
		$model->delete();
		return $this->redirect(['index']);
	}

	public function actionCommentstatus($action, $id) {
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

	protected function findComment($id) {
		$query = Comment::find()
				->where(['id' => $id])
				->andWhere(Yii::$app->user->isGuest ? ['`active`' => Post::STATUS_ACTIVE] : ['or', ['`active`' => [Post::STATUS_INACTIVE, Post::STATUS_ACTIVE]]])
				->with('user');

		$model = $query->one();

		if ($model === null)
			throw new NotFoundHttpException('Page not found.');

		$post = $this->findModel($id);
		if (!$post->belongsToViewer())
			throw new UnauthorizedHttpException('You do not have permission to edit this comment.');

		return $model;
	}

	protected function findModel($id, $withList = []) {
		$query = Post::find()
				->where(['id' => $id])
				->andWhere(Yii::$app->user->isGuest ? ['active' => Post::STATUS_ACTIVE] : ['or', ['active' => [Post::STATUS_INACTIVE, Post::STATUS_ACTIVE]]])
				->with('user');

		foreach ($withList as $with)
			$query->with($with);

		$model = $query->one();

		if ($model === null)
			throw new NotFoundHttpException('Page not found.');

		return $model;
	}
}
