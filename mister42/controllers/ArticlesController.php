<?php
namespace app\controllers;
use Yii;
use app\models\articles\{Articles, ArticlesComments};
use yii\bootstrap4\Html;
use yii\filters\{AccessControl, VerbFilter};
use yii\helpers\Url;
use yii\web\{MethodNotAllowedHttpException, NotFoundHttpException, UnauthorizedHttpException};

class ArticlesController extends \yii\web\Controller {
	public $layout = 'columns';

	public function behaviors(): array {
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
					'deletecomment' => ['post'],
					'newcomment' => ['post'],
				],
			],
		];
	}

	public function actionIndex(): string {
		$model = new Articles();
		return $this->render('index', [
			'model' => $model,
		]);
	}

	public function actionArticle(int $id): string {
		$model = Articles::find()
			->where(['id' => $id])
			->with(['author', 'comments'])
			->one();

		if (!$model)
			throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));

		if (Yii::$app->request->get('title') !== $model->url)
			$this->redirect(['article', 'id' => $model->id, 'title' => $model->url], 301)->send();

		Yii::$app->view->registerLinkTag(['rel' => 'canonical', 'href' => Url::to(['permalink/articles', 'id' => $model->id])]);
		if ($model->pdf)
			Yii::$app->view->registerLinkTag(['rel' => 'alternate', 'href' => Url::to(['pdf', 'id' => $model->id, 'title' => $model->url], true), 'type' => 'application/pdf', 'title' => $model->title]);

		return $this->render('view', [
			'model' => $model,
		]);
	}

	public function actionPdf(int $id, string $title = ''): void {
		$model = Articles::find()
			->where(['id' => $id])
			->with(['author'])
			->one();

		if (empty($title) || $title != $model->url)
			$this->redirect(['pdf', 'id' => $model->id, 'title' => $model->url], 301)->send();

		if (!$model->pdf)
			throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));

		$fileName = Articles::buildPdf($model);
		Yii::$app->response->sendFile($fileName, implode(' - ', [Yii::$app->name, $model->url]).'.pdf');
	}

	public function actionCreate(): string {
		$model = new Articles();
		return $this->doFormArticle($model);
	}

	public function actionUpdate(int $id): string {
		$model = Articles::findOne(['id' => $id]);
		if (!$model->belongsToViewer())
			throw new UnauthorizedHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));

		return $this->doFormArticle($model);
	}

	public function actionDelete(int $id): void {
		$model = Articles::findOne(['id' => $id]);
		if (!$model->belongsToViewer())
			throw new UnauthorizedHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));

		$model->delete();
		$this->redirect(['index'])->send();
	}

	public function actionNewcomment(int $id): string {
		$article = Articles::find()
			->where(['id' => $id])
			->with('author')
			->one();

		$comment = new ArticlesComments;
		$comment->load(Yii::$app->request->post());
		$comment->parent = $id;

		if ($comment->saveComment($comment)) :
			if (!Yii::$app->user->isGuest) :
				$comment->name = Yii::$app->user->identity->username;
				$comment->email = Yii::$app->user->identity->email;
			endif;
			$comment->sendCommentMail($article, $comment);
			return Html::tag('div', Yii::t('mr42', 'Your comment has been saved. It will not be visible until approved by an administrator.'), ['class' => 'alert alert-success']);
		endif;
		return Html::tag('div', Yii::t('mr42', 'Something went wrong, Your comment has not been saved.'), ['class' => 'alert alert-danger']);
	}

	public function actionDeletecomment(int $id): void {
		$comment = ArticlesComments::findOne(['id' => $id]);
		$comment->active = $comment->active ? 0 : 1;

		$article = Articles::findOne(['id' => $comment->parent]);
		if (!$article->belongsToViewer())
			throw new UnauthorizedHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));

		$comment->delete();
		$this->redirect(['article', 'id' => $article->id, 'title' => $article->title, '#' => 'comments'])->send();
	}

	public function actionTogglecomment(int $id) {
		$comment = ArticlesComments::findOne(['id' => $id]);
		$comment->active = $comment->active ? 0 : 1;

		$article = Articles::findOne(['id' => $comment->parent]);
		if (!$article->belongsToViewer())
			throw new UnauthorizedHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));

		if (!Yii::$app->request->isAjax)
			throw new MethodNotAllowedHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));

		$comment->update();
		return $comment->showApprovalButton();
	}

	public function actionSearch(string $q) {
		$model = new Articles();
		$query = $model->find()
			->orderBy('updated DESC')
			->where(['like', 'title', $q])
			->orWhere(['like', 'content', $q]);

		return $this->render('index', [
			'model' => $model,
			'query' => $query,
			'q' => $q,
		]);
	}

	public function actionTag(string $tag) {
		$model = new Articles();
		$query = $model->find()
			->orderBy('updated DESC')
			->where(['like', 'tags', $tag]);

		return $this->render('index', [
			'model' => $model,
			'query' => $query,
			'tag' => $tag,
		]);
	}

	private function doFormArticle(Articles $model): string {
		if ($model->load(Yii::$app->request->post())) :
			if ($model->validate() && $model->save())
				$this->redirect(['article', 'id' => $model->id, 'title' => $model->url])->send();
		endif;

		$this->layout = '@app/views/layouts/main.php';
		return $this->render('_formArticle', [
			'model' => $model,
		]);
	}
}
