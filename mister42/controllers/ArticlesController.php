<?php
namespace app\controllers;
use Yii;
use app\models\articles\{Articles, BaseArticles, Comments};
use yii\bootstrap4\Html;
use yii\data\ActiveDataProvider;
use yii\filters\{AccessControl, VerbFilter};
use yii\helpers\Url;
use yii\web\{MethodNotAllowedHttpException, NotFoundHttpException, UnauthorizedHttpException};

class ArticlesController extends \yii\web\Controller {
	public $layout = 'columns';

	public function behaviors() {
		return [
			'access' => [
				'class' => AccessControl::class,
				'only' => ['create', 'update', 'delete', 'commentstatus'],
				'rules' => [
					[
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'delete' => ['post'],
				],
			],
		];
	}

	public function actionIndex(int $id = 0, string $action = '', string $q = ''): string {
		if ($id !== 0) {
					return self::pageIndex($id);
		}

		$query = Articles::find()->orderBy('id DESC');
		if ($action === "tag" && !empty($q)) {
					$query->where(['like', 'tags', $q]);
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
			'q' => $q,
		]);
	}

	public function actionPdf(int $id, string $title = '') {
		$model = $this->findModel($id);

		if (!$model->pdf) {
					throw new NotFoundHttpException('File not found.');
		}

		if (empty($title) || $title != $model->url) {
					$this->redirect(['pdf', 'id' => $model->id, 'title' => $model->url], 301)->send();
		}

		$html = $this->renderPartial('pdf', ['model' => $model]);
		$fileName = Articles::buildPdf($model, $html);
		Yii::$app->response->sendFile($fileName, implode(' - ', [Yii::$app->name, $model->url]).'.pdf');
	}

	public function actionCreate() {
		$this->layout = '@app/views/layouts/main.php';

		$model = new BaseArticles;
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
					return $this->redirect(['index', 'id' => $model->id, 'title' => $model->url]);
		}

		return $this->render('_formArticles', [
			'action' => 'create',
			'model' => $model,
		]);
	}

	public function actionUpdate(int $id) {
		$this->layout = '@app/views/layouts/main.php';

		$model = BaseArticles::findOne(['id' => $id]);
		if (!$model->belongsToViewer()) {
					throw new UnauthorizedHttpException('You do not have permission to edit this article.');
		}

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
					return $this->redirect(['index', 'id' => $model->id, 'title' => $model->url]);
		}

		return $this->render('_formArticles', [
			'action' => 'edit',
			'model' => $model,
		]);
	}

	public function actionDelete(int $id) {
		$model = BaseArticles::findOne(['id' => $id]);
		if (!$model->belongsToViewer()) {
					throw new UnauthorizedHttpException('You do not have permission to delete this article.');
		}

		$model->delete();
		return $this->redirect(['index']);
	}

	public function actionCommentstatus(int $id, string $action) {
		$comment = Comments::findOne(['id' => $id]);
		$article = $this->findModel($comment->parent);

		if (!$article->belongsToViewer()) {
					throw new UnauthorizedHttpException('You do not have permission to edit this comment.');
		}

		$comment->active = $comment->active ? Comments::STATUS_INACTIVE : Comments::STATUS_ACTIVE;
		if ($action == "toggleapproval") {
			if (!Yii::$app->request->isAjax) {
							throw new MethodNotAllowedHttpException('Method Not Allowed.');
			}
			$comment->update();
			return $comment->showApprovalButton();
		} elseif ($action == "delete") {
			$comment->delete();
			return $this->redirect(['index', 'id' => $article->id, 'title' => $article->title, '#' => 'comments']);
		}

		return false;
	}

	private function pageIndex(int $id): string {
		$model = $this->findModel($id, ['comments']);
		$comment = new Comments;

		if ($comment->load(Yii::$app->request->post())) {
			if ($model->addComment($comment)) {
				if (!Yii::$app->user->isGuest) {
					$comment->name = Yii::$app->user->identity->username;
					$comment->email = Yii::$app->user->identity->email;
				}
				$comment->sendCommentMail($model, $comment);
				return Html::tag('div', 'Your comment has been saved. It will not be visible until approved by an administrator.', ['class' => 'alert alert-success']);
			}
			return Html::tag('div', 'Something went wrong, Your comment has not been saved.', ['class' => 'alert alert-danger']);
		}

		if (Yii::$app->request->get('title') != $model->url) {
					$this->redirect(['index', 'id' => $model->id, 'title' => $model->url], 301)->send();
		}

		Yii::$app->view->registerLinkTag(['rel' => 'canonical', 'href' => Yii::$app->params['shortDomain']."art{$model->id}"]);
		if ($model->pdf) {
					Yii::$app->view->registerLinkTag(['rel' => 'alternate', 'href' => Url::to(['pdf', 'id' => $model->id, 'title' => $model->url], true), 'type' => 'application/pdf', 'title' => $model->title]);
		}

		return $this->render('view', [
			'model' => $model,
			'comment' => $comment
		]);
	}

	protected function findComment(int $id) {
		$model = Comments::find()
			->where(['id' => $id])
			->with('user')
			->one();

		if (!$model) {
					throw new NotFoundHttpException('Page not found.');
		}

		$article = $this->findModel($id);
		if (!$article->belongsToViewer()) {
					throw new UnauthorizedHttpException('You do not have permission to edit this comment.');
		}

		return $model;
	}

	protected function findModel(int $id, array $withList = []) {
		$query = Articles::find()
			->where(['id' => $id])
			->with('user');

		foreach ($withList as $with) {
					$query->with($with);
		}

		$model = $query->one();

		if (!$model) {
					throw new NotFoundHttpException('Page not found.');
		}

		return $model;
	}
}
