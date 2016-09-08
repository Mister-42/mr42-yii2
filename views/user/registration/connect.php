<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Sign in';
$this->params['breadcrumbs'][] = 'User';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
		<h3><?= Html::encode($this->title) ?></h3>

		<div class="alert alert-info">
			<p>In order to finish your registration, we need you to enter following fields</p>
		</div>

		<?php $form = ActiveForm::begin([
			'id' => 'connect-account-form',
		]); ?>

		<?= $form->field($model, 'email', [
			'template' => '{label}<div class="input-group"><span class="input-group-addon"><span class="addon-email"></span></span>{input}</div>{error}',
		]) ?>

		<?= $form->field($model, 'username', [
			'template' => '{label}<div class="input-group"><span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>{input}</div>{error}',
		]) ?>

		<?= Html::submitButton('Continue', ['class' => 'btn btn-primary btn-block']) ?>

		<?php ActiveForm::end(); ?>

		<p class="text-center">
			<?= Html::a('If you already registered, sign in and connect this account on settings page', ['/user/settings/networks']) ?>.
		</p>
	</div>
</div>
