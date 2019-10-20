<?php

namespace app\models\articles;

use app\models\user\User;
use Da\User\Validator\ReCaptchaValidator;
use mister42\Secrets;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\bootstrap4\Html;

class ArticlesComments extends \yii\db\ActiveRecord
{
    public $captcha;
    public $parsedContent;

    public function afterFind(): void
    {
        parent::afterFind();
        $this->parsedContent = Yii::$app->formatter->cleanInput($this->content, 'gfm-comment');
    }

    public function attributeLabels(): array
    {
        return [
            'title' => Yii::t('mr42', 'Title'),
            'content' => Yii::t('mr42', 'Content'),
            'name' => Yii::t('mr42', 'Name'),
            'email' => Yii::t('mr42', 'Email Address'),
            'website' => Yii::t('mr42', 'Website URL'),
        ];
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created',
                'updatedAtAttribute' => null,
            ],
        ];
    }

    public static function find()
    {
        return new Query(get_called_class());
    }

    public function getArticle()
    {
        return $this->hasOne(Articles::class, ['id' => 'parent']);
    }

    public function getAuthor()
    {
        return $this->hasOne(User::class, ['id' => 'user']);
    }

    public function getCommentReplies()
    {
        return $this->hasMany(self::className(), ['parent_comment' => 'id']);
    }

    public static function getLastModified(): int
    {
        return self::find()->max('created');
    }

    public function rules(): array
    {
        $rules = [
            [['parent', 'title', 'content'], 'required'],
            [['parent', 'created', 'user', 'active'], 'integer'],
            ['content', 'string'],
            'charCount' => ['content', 'string', 'max' => 4096],
            [['title', 'website'], 'string', 'max' => 128],
            ['name', 'string', 'max' => 25],
            ['email', 'string', 'max' => 50],
            [['name', 'email', 'website'], 'default', 'value' => null],
            ['active', 'default', 'value' => 0],
            ['user', 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user' => 'id']],
            ['parent', 'exist', 'skipOnError' => false, 'targetClass' => Articles::class, 'targetAttribute' => ['parent' => 'id']],
        ];

        if (Yii::$app->user->isGuest) {
            $rules[] = ['captcha', ReCaptchaValidator::class];
            $rules[] = [['name', 'email'], 'required'];
        }
        return $rules;
    }

    public function sendCommentMail(Articles $model, self $comment): void
    {
        $secrets = (new Secrets())->getValues();
        Yii::$app->mailer->compose(
            ['text' => 'commentToAuthor'],
            ['model' => $model, 'comment' => $comment]
        )
            ->setTo([$model->author->email => $model->author->username])
            ->setFrom([$comment->email => $comment->name])
            ->setSubject("A new comment has been posted on '{$model->title}'.")
            ->send();

        if (Yii::$app->user->isGuest) {
            Yii::$app->mailer->compose(
                ['html' => 'commentToCommenter'],
                ['model' => $model, 'comment' => $comment]
            )
                ->setTo([$comment->email => $comment->name])
                ->setFrom([$secrets['params']['noreplyEmail'] => Yii::$app->name])
                ->setSubject("Thank you for your reply on '{$model->title}'.")
                ->send();
        }
    }

    public function showApprovalButton(): string
    {
        return Html::a(
            $this->active
                ? Yii::$app->icon->name('thumbs-down')->class('mr-1') . Yii::t('mr42', 'Renounce')
                : Yii::$app->icon->name('thumbs-up')->class('mr-1') . Yii::t('mr42', 'Approve'),
            ['togglecomment', 'id' => $this->id],
            ['class' => $this->active ? 'btn btn-sm btn-outline-warning ml-1' : 'btn btn-sm btn-outline-success ml-1']
        );
    }

    public static function tableName(): string
    {
        return '{{%articles_comments}}';
    }
}
