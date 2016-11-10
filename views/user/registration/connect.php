<?php
use yii\bootstrap\{ActiveForm, Html};

$this->title = 'Sign in';
$this->params['breadcrumbs'][] = 'User';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
		<?= Html::tag('h3', Html::encode($this->title)) ?>

		<div class="alert alert-info">
			<p>In order to finish your registration, we need you to enter following fields</p>
		</div>

		<?php $form = ActiveForm::begin([
			'id' => 'connect-account-form',
		]);

		echo $form->field($model, 'email', [
			'inputOptions' => ['tabindex' => 1],
			'template' => '{label}<div class="input-group"><span class="input-group-addon"><span class="addon-email"></span></span>{input}</div>{error}',
		]);

		echo $form->field($model, 'username', [
			'inputOptions' => ['tabindex' => 2],
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('user').'</span>{input}</div>{error}',
		]);

		echo Html::submitButton('Continue', ['class' => 'btn btn-primary btn-block', 'tabindex' => 3]);

		ActiveForm::end(); ?>

		<p class="text-center">
			<?= Html::a('If you already registered, sign in and connect this account on settings page', ['/user/settings/networks']) ?>.
		</p>
	</div>
</div>
