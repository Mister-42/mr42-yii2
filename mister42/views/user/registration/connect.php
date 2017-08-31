<?php
use yii\bootstrap\{Alert, ActiveForm, Html};

$this->title = Yii::t('usuario', 'Sign in');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3"><?php
		echo Html::tag('h3', Html::encode($this->title));

		echo Alert::widget([
			'options' => ['class' => 'alert-info'],
			'body' => Yii::t('usuario', 'In order to finish your registration, we need you to enter following fields'),
		]);

		$form = ActiveForm::begin([
			'id' => $model->formName(),
		]);

		echo $form->field($model, 'email', [
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('envelope').'</span>{input}</div>{error}',
		])->input('email', ['tabindex' => 1]);

		echo $form->field($model, 'username', [
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('user').'</span>{input}</div>{error}',
		])->textInput(['tabindex' => 2]);

		echo Html::submitButton(Yii::t('usuario', 'Continue'), ['class' => 'btn btn-success btn-block', 'tabindex' => 3]);

		ActiveForm::end();

		echo Html::tag('p', Html::a(Yii::t('usuario', 'If you already registered, sign in and connect this account on settings page'), ['/user/settings/networks']), ['class' => 'text-center']);
	?></div>
</div>
