<?php
use dektrium\user\widgets\Connect;
use yii\bootstrap\{ActiveForm, Html};

$this->title = 'Login';
$this->params['breadcrumbs'][] = 'User';
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="row">
	<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
		<h2 class="panel-title"><?= Html::encode($this->title) ?></h2>

		<?php $form = ActiveForm::begin([
			'id'						=> 'login-form',
			'enableAjaxValidation'		=> true,
			'enableClientValidation'	=> false,
			'validateOnBlur'			=> false,
			'validateOnType'			=> false,
			'validateOnChange'			=> false,
		]);

		echo $form->field($model, 'login', [
			'inputOptions' => ['autofocus' => true, 'class' => 'form-control', 'tabindex' => 1],
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('user').'</span>{input}</div>{error}',
		]);

		echo $form->field($model, 'password', [
			'inputOptions' => ['class' => 'form-control', 'tabindex' => 2],
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('lock').'</span>{input}</div>{error}',
		])->passwordInput()
		->label('Password' . ($module->enablePasswordRecovery ? ' (' . Html::a('Forgot password?', ['/user/recovery/request']) . ')' : ''));

		echo $form->field($model, 'rememberMe')->checkbox(['tabindex' => 3]);

		echo Html::submitButton('Sign in', ['class' => 'btn btn-primary btn-block', 'tabindex' => 4]);

		ActiveForm::end();

		if ($module->enableConfirmation): ?>
			<p class="text-center"><br>
				<?= Html::a('Didn\'t receive confirmation message?', ['/user/registration/resend']) ?>
			</p>
		<?php endif ?>
		<?php if ($module->enableRegistration): ?>
			<p class="text-center">
				<?= Html::a('Don\'t have an account? Sign up!', ['/user/registration/register']) ?>
			</p>
		<?php endif ?>
		<?= Connect::widget([
			'baseAuthUrl' => ['/user/security/auth'],
		]) ?>
	</div>
</div>
