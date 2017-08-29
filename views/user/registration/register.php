<?php
use yii\bootstrap\{ActiveForm, Html};
use yii\captcha\Captcha;

$this->title = Yii::t('usuario', 'Sign up');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
		<h3><?= Html::encode($this->title) ?></h3><?php

		$form = ActiveForm::begin(
			[
				'id' => $model->formName(),
				'enableAjaxValidation' => true,
				'enableClientValidation' => false,
			]
		);

		echo $form->field($model, 'email', [
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('envelope').'</span>{input}</div>{error}',
		])->input('email', ['tabindex' => 1]);

		echo '<div class="row">';
			echo $form->field($model, 'username', [
				'options' => ['class' => 'col-xs-6 form-group'],
				'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('user').'</span>{input}</div>{error}',
			])->textInput(['tabindex' => 2]);

			if ($module->generatePasswords === false)
				echo $form->field($model, 'password', [
					'options' => ['class' => 'col-xs-6 form-group'],
					'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('lock').'</span>{input}</div>{error}',
				])->passwordInput(['tabindex' => 3]);
		echo '</div>';

		echo $form->field($model, 'captcha')->widget(Captcha::className(), [
			'captchaAction' => '/site/captcha',
			'imageOptions' => ['alt' => 'CAPTCHA image', 'class' => 'captcha'],
			'options' => ['class' => 'form-control', 'tabindex' => 4],
			'template' => '<div class="row"><div class="col-xs-6"><div class="input-group"><span class="input-group-addon">'.Html::icon('dashboard').'</span>{input}</div></div> {image}</div>',
		])->hint('Click on the image to retrieve a new verification code.');

		echo Html::submitButton(Yii::t('usuario', 'Sign up'), ['class' => 'btn btn-success btn-block', 'tabindex' => 5]);

		ActiveForm::end();
		?><p class="text-center"><?= Html::a(Yii::t('usuario', 'Already registered? Sign in!'), ['/user/security/login']) ?></p>
	</div>
</div>
