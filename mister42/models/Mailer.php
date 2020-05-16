<?php

namespace mister42\models;

use mister42\Secrets;
use Yii;
use yii\swiftmailer\Message;

class Mailer
{
    public static function compose(string $recipient, string $subject, string $template, string $code): Message
    {
        $secrets = (new Secrets())->getValues();
        return Yii::$app->mailer
            ->compose([$code => $template])
            ->setTo($recipient)
            ->setFrom([$secrets['params']['noreplyEmail'] => Yii::$app->name])
            ->setSubject($subject);
    }

    public static function sendFileHtml(string $recipient, string $subject, string $template, array $file): bool
    {
        $mail = self::compose($recipient, $subject, $template, 'html');
        $mail->attach(Yii::getAlias($file['file']), ['fileName' => $file['name']]);
        return $mail->send();
    }
}
