<?php

namespace mister42\models\my;

use Da\User\Validator\ReCaptchaValidator;
use mister42\Secrets;
use Yii;

class Contact extends \yii\base\Model
{
    public $attachment;
    public $captcha;
    public $content;
    public $email;
    public $name;
    public $title;

    public function attributeLabels(): array
    {
        return [
            'name' => Yii::t('mr42', 'Name'),
            'email' => Yii::t('mr42', 'Email Address'),
            'title' => Yii::t('mr42', 'Subject'),
            'content' => Yii::t('mr42', 'Message'),
            'attachment' => Yii::t('mr42', 'Attachment'),
            'captcha' => Yii::t('mr42', 'CAPTCHA'),
        ];
    }

    public function rules(): array
    {
        return [
            [['name', 'email', 'title', 'content'], 'required'],
            [['name', 'email', 'title', 'content'], 'trim'],
            'charCount' => ['content', 'string', 'max' => 8192],
            ['name', 'string', 'max' => 25],
            ['email', 'string', 'max' => 50],
            ['email', 'email', 'checkDNS' => true, 'enableIDN' => true],
            ['attachment', 'file', 'minSize' => 64, 'maxSize' => 1024 * 1024 * 5],
            ['captcha', ReCaptchaValidator::class],
        ];
    }

    public function sendEmail(): bool
    {
        $secrets = (new Secrets())->getValues();
        $mailer = Yii::$app->mailer->compose()
            ->setTo($secrets['params']['adminEmail'])
            ->setFrom([$this->email => $this->name])
            ->setSubject(implode(' - ', [Yii::$app->name, Yii::t('mr42', 'Contact'), $this->title]))
            ->setTextBody($this->content);

        if ($this->attachment) {
            $mailer->attach($this->attachment->tempName, ['fileName' => $this->attachment->name]);
        }

        return $mailer->send();
    }
}
