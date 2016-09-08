<?php
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use yii\helpers\Html;

$this->title = 'Sign up';
$this->params['breadcrumbs'][] = 'User';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-md-offset-3 col-md-6">
		<h2 class="panel-title"><?= Html::encode($this->title) ?></h2>
		<?php $form = ActiveForm::begin([
			'id' => 'registration-form',
		]);

		echo $form->field($model, 'email', [
			'template' => '{label}<div class="input-group"><span class="input-group-addon"><span class="addon-email"></span></span>{input}</div>{error}',
		])->textInput(['tabindex' => 1]);

		echo '<div class="row">';
			echo $form->field($model, 'username', [
				'options' => ['class' => 'col-xs-6 form-group'],
				'template' => '{label}<div class="input-group"><span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>{input}</div>{error}',
			])->textInput(['tabindex' => 2]);

			echo $form->field($model, 'password', [
				'options' => ['class' => 'col-xs-6 form-group'],
				'template' => '{label}<div class="input-group"><span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>{input}</div>{error}',
			])->passwordInput(['tabindex' => 3]);
		echo '</div>';

		echo $form->field($model, 'captcha')->widget(Captcha::className(), [
			'captchaAction' => '/tech/captcha',
			'imageOptions' => ['alt' => 'CAPTCHA image', 'class' => 'captcha'],
			'options' => ['class' => 'form-control', 'tabindex' => 4],
			'template' => '<div class="row"><div class="col-xs-4"><div class="input-group"><span class="input-group-addon"><span class="glyphicon glyphicon-dashboard"></span></span>{input}</div></div> {image}</div>',
		])->hint('Click on the image to retrieve a new verification code.');

		echo Html::submitButton('Sign up', ['class' => 'btn btn-primary btn-block', 'tabindex' => 5]);

		ActiveForm::end(); ?>

		<p class="text-center"><br /><?= Html::a('Already registered? Sign in!', ['/user/security/login']) ?></p>
	</div>
</div>
