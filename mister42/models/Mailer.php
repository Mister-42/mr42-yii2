<?php
namespace app\models;
use Yii;
use yii\swiftmailer\Message;

class Mailer {
	public static function sendFileHtml(string $recipient, string $subject, string $template, array $file): bool {
		$mail = self::compose($recipient, $subject, $template, 'html');
		$mail->attach(Yii::getAlias($file['file']), ['fileName' => $file['name']]);
		return self::send($mail);
	}

	public static function compose(string $recipient, string $subject, string $template, string $code): Message {
		return Yii::$app->mailer
			->compose([$code => $template])
			->setTo($recipient)
			->setFrom([Yii::$app->params['secrets']['params']['noreplyEmail'] => Yii::$app->name])
			->setSubject($subject);
	}

	private static function send(Message $mail): bool {
		return $mail->send();
	}
}
