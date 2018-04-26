<?php
use app\models\Icon;
use yii\bootstrap4\{Alert, ActiveForm, Html};

$this->title = Yii::t('usuario', 'Sign in');
$this->params['breadcrumbs'][] = $this->title;

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-sm-12 col-md-6 mx-auto']);
		echo Html::tag('h3', $this->title);

		echo Alert::widget([
			'options' => ['class' => 'alert-info'],
			'body' => Yii::t('usuario', 'In order to finish your registration, we need you to enter following fields'),
		]);

		$form = ActiveForm::begin([
			'id' => $model->formName(),
		]);

		echo $form->field($model, 'email', [
			'template' => '{label}<div class="input-group">'.Icon::fieldAddon('at').'{input}</div>{error}',
		])->input('email', ['tabindex' => 1]);

		echo $form->field($model, 'username', [
			'template' => '{label}<div class="input-group">'.Icon::fieldAddon('user').'{input}</div>{error}',
		])->textInput(['tabindex' => 2]);

		echo Html::submitButton(Yii::t('usuario', 'Continue'), ['class' => 'btn btn-success', 'tabindex' => 3]);

		ActiveForm::end();

		echo Html::tag('p', Html::a(Yii::t('usuario', 'If you already registered, sign in and connect this account on settings page'), ['/user/settings/networks']), ['class' => 'text-center']);
	echo Html::endTag('div');
echo Html::endTag('div');
