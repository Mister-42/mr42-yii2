<?php
namespace app\models;
use yii\bootstrap4\{ActiveForm, Html};
use yii\captcha\Captcha;

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
}
