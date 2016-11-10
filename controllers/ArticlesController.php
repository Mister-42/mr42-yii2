<?php
namespace app\controllers;
use Yii;
use app\models\articles\{Articles, BaseArticles, Comments};
use yii\bootstrap\Alert;
use yii\data\ActiveDataProvider;
use yii\filters\{AccessControl, VerbFilter};
use yii\helpers\Url;
use yii\web\{Controller, MethodNotAllowedHttpException, NotFoundHttpException, UnauthorizedHttpException};

class ArticlesController extends Controller {
	public $layout = '@app/views/layouts/column2.php';

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
			$comment = new Comments;

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

		$query = Articles::find()->orderBy('id DESC');
		if ($action === "tag" && !empty($tag)) {
			$query->where(['like', 'tags', $tag]);
		} elseif ($action === "search" && !empty($q)) {
			Yii::$app->view->registerMetaTag(['name' => 'robots', 'content' => 'noindex']);
			$query->where(['like', 'title', $q]);
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

			$html = $this->renderPartial('pdf', ['model' => $model]);

			$fileName = Articles::buildPdf($model, $html);
			Yii::$app->response->sendFile($fileName, implode(' - ', [Yii::$app->name, $model->url]).'.pdf');
		}
	}

	public function actionCreate() {
		$this->layout = '@app/views/layouts/main.php';

		$model = new BaseArticles;
		if ($model->load(Yii::$app->request->post()) && $model->save())
			return $this->redirect(['index', 'id' => $model->id, 'title' => $model->url]);

		return $this->render('_formArticles', [
			'action' => 'create',
			'model' => $model,
		]);
	}

	public function actionUpdate($id) {
		$this->layout = '@app/views/layouts/main.php';

		$model = BaseArticles::findOne($id);
		if (!$model->belongsToViewer())
			throw new UnauthorizedHttpException('You do not have permission to edit this article.');

		if ($model->load(Yii::$app->request->post()) && $model->save())
			return $this->redirect(['index', 'id' => $model->id, 'title' => $model->url]);

		return $this->render('_formArticles', [
			'action' => 'edit',
			'model' => $model,
		]);
	}

	public function actionDelete($id) {
		$model = BaseArticles::findOne($id);
		if (!$model->belongsToViewer())
			throw new UnauthorizedHttpException('You do not have permission to delete this article.');

		$model->delete();
		return $this->redirect(['index']);
	}

	public function actionCommentstatus($action, $id) {
		$comment = Comments::findOne($id);
		$article = $this->findModel($comment->parent);

		if (!$article->belongsToViewer())
			throw new UnauthorizedHttpException('You do not have permission to edit this comment.');

		$comment->active = ($comment->active) ? Comments::STATUS_INACTIVE : Comments::STATUS_ACTIVE;
		if ($action == "toggleapproval") {
			if (!Yii::$app->request->isAjax)
				throw new MethodNotAllowedHttpException('Method Not Allowed.');
			$comment->update();
			return $comment->showApprovalButton();
		} elseif ($action == "delete") {
			$comment->delete();
			return $this->redirect(['index', 'id' => $article->id, 'title' => $article->title, '#' => 'comments']);
		}

		return false;
	}

	protected function findComment($id) {
		$model = Comments::find()
			->where(['id' => $id])
			->with('user')
			->one();

		if (!$model)
			throw new NotFoundHttpException('Page not found.');

		$article = $this->findModel($id);
		if (!$article->belongsToViewer())
			throw new UnauthorizedHttpException('You do not have permission to edit this comment.');

		return $model;
	}

	protected function findModel($id, $withList = []) {
		$query = Articles::find()
			->where(['id' => $id])
			->with('user');

		foreach ($withList as $with)
			$query->with($with);

		$model = $query->one();

		if (!$model)
			throw new NotFoundHttpException('Page not found.');

		return $model;
	}
}
