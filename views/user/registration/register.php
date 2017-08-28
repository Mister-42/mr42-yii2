<?php
use yii\bootstrap\{ActiveForm, Html};
use yii\captcha\Captcha;

$this->title = Yii::t('usuario', 'Sign up');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
			</div>
			<div class="panel-body">
				<?php $form = ActiveForm::begin(
					[
						'id' => $model->formName(),
						'enableAjaxValidation' => true,
						'enableClientValidation' => false,
					]
				);

				echo $form->field($model, 'email');

				echo $form->field($model, 'username');

				if ($module->generatePasswords == false) {
					echo $form->field($model, 'password')->passwordInput();
				}

				echo $form->field($model, 'captcha')->widget(Captcha::className(), [
					'captchaAction' => '/site/captcha',
					'imageOptions' => ['alt' => 'CAPTCHA image', 'class' => 'captcha'],
					'options' => ['class' => 'form-control', 'tabindex' => 4],
					'template' => '<div class="row"><div class="col-xs-6"><div class="input-group"><span class="input-group-addon">'.Html::icon('dashboard').'</span>{input}</div></div> {image}</div>',
				])->hint('Click on the image to retrieve a new verification code.');

				echo Html::submitButton(Yii::t('usuario', 'Sign up'), ['class' => 'btn btn-success btn-block']);

				ActiveForm::end(); ?>
			</div>
		</div>
		<p class="text-center">
			<?= Html::a(Yii::t('usuario', 'Already registered? Sign in!'), ['/user/security/login']) ?>
		</p>
	</div>
</div>
