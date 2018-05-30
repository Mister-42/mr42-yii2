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
			'imageOptions' => ['alt' => Yii::t('mr42', 'CAPTCHA image'), 'class' => 'captcha'],
			'options' => ['class' => 'form-control', 'tabindex' => $tab],
			'template' => '<div class="row"><div class="col-6"><div class="input-group">'.Yii::$app->icon->fieldAddon('code').'{input}</div></div> {image}</div>',
		])->hint(Yii::t('mr42', 'Click on the image to retrieve a new verification code.'))
		->label(Html::tag('abbr', $model->getAttributeLabel('captcha'), ['title' => Yii::t('mr42', 'Completely Automated Public Turing test to tell Computers and Humans Apart')]));
	}

	public static function charCount($model, int $chars) {
		$model->registerJs("var formCharCount = {chars:{$chars}, lang:{overLimit:'".Yii::t('mr42', '{x} characters over the limit', ['x' => Html::tag('span', null, ['class' => 'charcount'])])."', charsLeft:'".Yii::t('mr42', '{x} characters left', ['x' => Html::tag('span', null, ['class' => 'charcount'])])."'}};".Yii::$app->formatter->jspack('formCharCounter.js'), View::POS_READY);
	}

	public static function togglePassword(ActiveForm $form, $model, View $view, array $options = []): string {
		$class = ArrayHelper::remove($options, 'class');
		$tab = ArrayHelper::remove($options, 'tab', 1);
		$view->registerJs("var togglePassword = {lang:{hide:'".Yii::t('mr42', 'Hide Password')."', show:'".Yii::t('mr42', 'Show Password')."'}};".Yii::$app->formatter->jspack('togglePassword.js'), View::POS_READY);
		return $form->field($model, 'password', [
			'options' => ['class' => implode(' ', ['form-group', $class])],
			'template' => '{label}<div class="input-group" id="pwdToggle">'.Yii::$app->icon->fieldAddon('lock').'{input}<span class="input-group-append">'.Html::button(Yii::$app->icon->show('eye', ['class' => 'append']).Yii::$app->icon->show('eye-slash', ['class' => 'd-none append']), ['class' => 'btn btn-primary', 'title' => Yii::t('mr42', 'Show Password')]).'</span></div>{error}',
		])->passwordInput(['tabindex' => ++$tab]);
	}

	public static function submitToolbar(string $text, int $tab): string {
		return Html::tag('div',
			Html::resetButton(Yii::t('mr42', 'Reset'), ['class' => 'btn btn-default ml-1', 'tabindex' => $tab + 2]).
			Html::submitButton($text, ['class' => 'btn btn-primary ml-1', 'tabindex' => ++$tab])
		, ['class' => 'btn-toolbar float-right form-group']);
	}
}
