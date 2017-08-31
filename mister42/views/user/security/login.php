<?php
use Da\User\Widget\ConnectWidget;
use yii\bootstrap\{ActiveForm, Html};

$this->title = Yii::t('usuario', 'Sign in');
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('@Da/User/resources/views/shared/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="row">
	<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
		<h3><?= Html::encode($this->title) ?></h3>

		<?php $form = ActiveForm::begin([
			'id'						=> $model->formName(),
			'enableAjaxValidation'		=> true,
			'enableClientValidation'	=> false,
			'validateOnBlur'			=> false,
			'validateOnType'			=> false,
			'validateOnChange'			=> false,
		]);

		echo $form->field($model, 'login', [
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('user').'</span>{input}</div>{error}',
		])->textInput(['autofocus' => true, 'class' => 'form-control', 'tabindex' => 1]);

		echo $form->field($model, 'password', [
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('lock').'</span>{input}</div>{error}',
		])->passwordInput(['class' => 'form-control', 'tabindex' => 2])
		->label(Yii::t('usuario', 'Password') . ($module->allowPasswordRecovery ? ' (' . Html::a(Yii::t('usuario', 'Forgot password?'), ['/user/recovery/request'], ['tabindex' => '5']) . ')' : ''));

		echo $form->field($model, 'rememberMe')->checkbox(['tabindex' => 3]);

		echo Html::submitButton(Yii::t('usuario', 'Sign in'), ['class' => 'btn btn-primary btn-block', 'tabindex' => 4]);

		ActiveForm::end();

		if ($module->enableEmailConfirmation)
			echo Html::tag('p', Html::a(Yii::t('usuario', 'Didn\'t receive confirmation message?'), ['/user/registration/resend']), ['class' => 'text-center']);

		if ($module->enableRegistration)
			echo Html::tag('p', Html::a(Yii::t('usuario', 'Don\'t have an account? Sign up!'), ['/user/registration/register']), ['class' => 'text-center']);

		echo ConnectWidget::widget(['baseAuthUrl' => ['/user/security/auth']]);
	?></div>
</div>
