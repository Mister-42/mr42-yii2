<?php
use app\models\Form;
use yii\bootstrap4\{ActiveForm, Html};

$this->title = Yii::t('usuario', 'Sign up');
$this->params['breadcrumbs'][] = $this->title;

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-sm-12 col-md-6 mx-auto']);
		echo Html::tag('h3', $this->title);

		$form = ActiveForm::begin(
			[
				'id' => $model->formName(),
				'enableAjaxValidation' => false,
				'enableClientValidation' => true,
			]
		);
		$tab = 0;

		echo $form->field($model, 'email', [
			'template' => '{label}<div class="input-group">'.Yii::$app->icon->fieldAddon('at').'{input}</div>{error}',
		])->input('email', ['tabindex' => ++$tab]);

		echo Html::beginTag('div', ['class' => 'row']);
			echo $form->field($model, 'username', [
				'options' => ['class' => 'col-6 form-group'],
				'template' => '{label}<div class="input-group">'.Yii::$app->icon->fieldAddon('user').'{input}</div>{error}',
			])->textInput(['tabindex' => ++$tab]);

			if ($module->generatePasswords === false) :
				echo $form->field($model, 'password', [
					'options' => ['class' => 'col-6 form-group'],
					'template' => '{label}<div class="input-group">'.Yii::$app->icon->fieldAddon('lock').'{input}</div>{error}',
				])->passwordInput(['tabindex' => ++$tab]);
			endif;
		echo Html::endTag('div');

		echo Form::captcha($form, $model, ++$tab);

		echo Html::submitButton(Yii::t('usuario', 'Sign up'), ['class' => 'btn btn-success btn-block', 'tabindex' => ++$tab]);

		ActiveForm::end();

		echo Html::tag('p', Html::a(Yii::t('usuario', 'Already registered? Sign in!'), ['/user/security/login']), ['class' => 'text-center']);
	echo Html::endTag('div');
echo Html::endTag('div');
