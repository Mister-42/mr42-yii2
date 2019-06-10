<?php
namespace app\models;
use Yii;
use yii\bootstrap4\Html;
use yii\web\View;

class ActiveForm extends \Thoulah\FontAwesomeInline\bootstrap4\ActiveForm {
	public function togglePassword($model, int $tab, array $options = []): string {
		$this->getView()->registerJs("var togglePassword = {lang:{hide:'".Yii::t('mr42', 'Hide Password')."', show:'".Yii::t('mr42', 'Show Password')."'}};".Yii::$app->formatter->jspack('togglePassword.js'), View::POS_READY);
		Html::addCssClass($options, 'form-group');
		return $this->field($model, 'password', [
			'inputTemplate' => '<div class="input-group" id="pwdToggle">'.Yii::$app->icon->activeFieldIcon('lock').'{input}<span class="input-group-append">'.Html::button(Yii::$app->icon->show('eye', ['class' => 'append']).Yii::$app->icon->show('eye-slash', ['class' => 'd-none append']), ['class' => 'btn btn-primary', 'title' => Yii::t('mr42', 'Show Password')]).'</span></div>',
			'options' => $options,
		])->passwordInput(['tabindex' => $tab]);
	}

	public function submitToolbar(string $text, int $tab): string {
		return Html::tag('div',
			Html::resetButton(Yii::t('mr42', 'Reset'), ['class' => 'btn btn-default ml-1', 'tabindex' => $tab + 2]).
			Html::submitButton($text, ['class' => 'btn btn-primary ml-1', 'tabindex' => ++$tab])
		, ['class' => 'btn-toolbar float-right form-group']);
	}
}
