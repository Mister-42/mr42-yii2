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
		</div><?php

		$form = ActiveForm::begin([
			'id' => 'connect-account-form',
		]);

		echo $form->field($model, 'email', [
			'template' => '{label}<div class="input-group"><span class="input-group-addon"><span class="addon-email"></span></span>{input}</div>{error}',
		])->input('email', ['tabindex' => 1]);

		echo $form->field($model, 'username', [
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('user').'</span>{input}</div>{error}',
		])->textInput(['tabindex' => 2]);

		echo Html::submitButton('Continue', ['class' => 'btn btn-primary btn-block', 'tabindex' => 3]);

		ActiveForm::end();

		echo Html::tag('p', Html::a('If you already registered, sign in and connect this account on settings page.', ['/user/settings/networks']), ['class' => 'text-center']);
	?></div>
</div>
