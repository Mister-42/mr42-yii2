<?php
namespace app\models;
use Yii;
use yii\bootstrap4\{ActiveForm, Html};
use yii\captcha\Captcha;
use yii\helpers\ArrayHelper;
use yii\web\View;

class Form extends ActiveForm {
	public static function captcha(ActiveForm $form, $model, int $tab): string {
		return $form->field($model, 'captcha')->widget(Captcha::class, [
			'captchaAction' => ['/site/captcha'],
			'imageOptions' => ['alt' => 'CAPTCHA image', 'class' => 'captcha'],
			'options' => ['class' => 'form-control', 'tabindex' => $tab],
			'template' => '<div class="row"><div class="col-6"><div class="input-group">'.Yii::$app->icon->fieldAddon('code').'{input}</div></div> {image}</div>',
		])->hint('Click on the image to retrieve a new verification code.')
		->label(Html::tag('abbr', $model->getAttributeLabel('captcha'), ['title' => 'Completely Automated Public Turing test to tell Computers and Humans Apart']));
	}

	public static function togglePassword(ActiveForm $form, $model, View $view, array $options = []): string {
		$class = ArrayHelper::remove($options, 'class');
		$tab = ArrayHelper::remove($options, 'tab', 1);
		$view->registerJs(Yii::$app->formatter->jspack('togglePassword.js'), View::POS_READY);
		return $form->field($model, 'password', [
			'options' => ['class' => $class],
			'template' => '{label}<div class="input-group" id="pwdToggle">'.Yii::$app->icon->fieldAddon('lock').'{input}<span class="input-group-append">'.Html::button(Yii::$app->icon->show('eye', ['class' => 'append']).Yii::$app->icon->show('eye-slash', ['class' => 'd-none append']), ['class' => 'btn btn-primary', 'title' => 'Show Password']).'</span></div>{error}',
		])->passwordInput(['tabindex' => ++$tab]);
	}
}
