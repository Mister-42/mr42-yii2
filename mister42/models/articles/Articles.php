<?php

namespace app\models\articles;

use app\models\Pdf;
use app\models\user\{Profile, User};
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\bootstrap4\Html;
use yii\helpers\{StringHelper, Url};
use yii\web\AccessDeniedHttpException;

class Articles extends \yii\db\ActiveRecord {
	public $contentParsed;

	public static function tableName(): string {
		return '{{%articles}}';
	}

	public function rules(): array {
		return [
			[['content', 'tags'], 'string'],
			[['created', 'updated', 'authorId', 'pdf', 'active'], 'integer'],
			[['title', 'content'], 'required'],
			[['authorId'], 'required'],
			[['title', 'url', 'source'], 'string', 'max' => 128],
			['source', 'url', 'enableIDN' => true],
			[['url', 'source'], 'default', 'value' => null],
			[['authorId'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['authorId' => 'id']],
			[['pdf', 'active'], 'boolean'],
		];
	}

	public function attributeLabels(): array {
		return [
			'title' => Yii::t('mr42', 'Title'),
			'url' => Yii::t('mr42', 'URL'),
			'content' => Yii::t('mr42', 'Content'),
			'source' => Yii::t('mr42', 'Source URL'),
			'tags' => Yii::t('mr42', 'Tags'),
			'pdf' => Yii::t('mr42', 'Create PDF'),
		];
	}

	public function behaviors(): array {
		return [
			[
				'class' => TimestampBehavior::class,
				'createdAtAttribute' => 'created',
				'updatedAtAttribute' => 'updated',
			],
		];
	}

	public function afterFind(): void {
		parent::afterFind();
		if (Yii::$app->controller->action->id !== 'update') {
			$this->url = $this->url ?? $this->title;
		}

		if ($this->content) {
			$this->contentParsed = Yii::$app->formatter->cleanInput($this->content, 'gfm', true);
			$this->contentParsed = str_replace(Html::tag('p', '[readmore]'), '[readmore]', $this->contentParsed);
		}
	}

	public function beforeDelete(): bool {
		return (parent::beforeDelete() && $this->belongsToViewer()) ? true : false;
	}

	public function beforeSave($insert): bool {
		if (!parent::beforeSave($insert)) {
			return false;
		}
		if (Yii::$app->user->isGuest) {
			throw new AccessDeniedHttpException('Please login.');
		}
		$this->url = !empty($this->url) ? $this->url : null;
		$this->source = !empty($this->source) ? $this->source : null;

		if ($insert) {
			$this->authorId = Yii::$app->user->id;
		} elseif (!$this->belongsToViewer()) {
			return false;
		}

		return true;
	}

	public static function buildPdf(self $model): string {
		$profile = (new Profile())->find($model->authorId)->one();
		$name = empty($profile->name) ? $model->user->username : $profile->name;
		$tags = Yii::t('mr42', '{results, plural, =1{1 tag} other{# tags}}', ['results' => count(StringHelper::explode($model->tags))]);
		$pdf = new Pdf();
		return $pdf->create(
			'@runtime/PDF/articles/' . sprintf('%05d', $model->id),
			str_replace('[readmore]', null, $model->contentParsed),
			$model->updated,
			[
				'author' => $name,
				'created' => $model->created,
				'footer' => implode('|', ["{$tags}: {$model->tags}", "Author: {$name}", 'Page {PAGENO} of {nb}']),
				'header' => implode('|', [Html::a(Yii::$app->name, Yii::$app->params['shortDomain']), Html::a($model->title, Yii::$app->urlManagerMr42->createUrl(['/permalink/articles', 'id' => $model->id])), date('D, j M Y', $model->updated)]),
				'keywords' => $model->tags,
				'subject' => $model->title,
				'title' => $model->title,
			]
		);
	}

	public function belongsToViewer(): bool {
		return $this->authorId === Yii::$app->user->id;
	}

	public static function getLastModified(): int {
		return self::find()->max('updated');
	}

	public function getAuthor() {
		return $this->hasOne(User::class, ['id' => 'authorId']);
	}

	public function getComments() {
		return $this->hasMany(ArticlesComments::class, ['parent' => 'id'])
			->onCondition(['parent_comment' => null])
			->with('commentReplies');
	}

	public static function find(): Query {
		return new Query(get_called_class());
	}
}
