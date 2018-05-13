<?php
use app\models\Icon;
use yii\bootstrap4\{ActiveForm, Html};

$this->title = Yii::t('usuario', 'Reset your password');
$this->params['breadcrumbs'][] = $this->title;

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-12 col-lg-6 mx-auto']);
		echo Html::tag('h3', $this->title);

		$form = ActiveForm::begin([
			'id' => $model->formName(),
			'enableAjaxValidation' => true,
			'enableClientValidation' => false,
		]);

		echo $form->field($model, 'email', [
			'inputTemplate' => '<div class="input-group">'.Icon::fieldAddon('lock').'{input}</div>',
		])->passwordInput(['autofocus' => true, 'tabindex' => ++$tab]);

		echo Html::submitButton(Yii::t('usuario', 'Continue'), ['class' => 'btn btn-primary btn-block', 'tabindex' => ++$tab]);

		ActiveForm::end();
	echo Html::endTag('div');
echo Html::endTag('div');
