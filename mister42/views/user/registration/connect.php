<?php
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
			'template' => '{label}<div class="input-group">'.Yii::$app->icon->fieldAddon('at').'{input}</div>{error}',
		])->input('email', ['tabindex' => ++$tab]);

		echo $form->field($model, 'username', [
			'template' => '{label}<div class="input-group">'.Yii::$app->icon->fieldAddon('user').'{input}</div>{error}',
		])->textInput(['tabindex' => ++$tab]);

		echo Html::submitButton(Yii::t('usuario', 'Continue'), ['class' => 'btn btn-success', 'tabindex' => ++$tab]);

		ActiveForm::end();

		echo Html::tag('p', Html::a(Yii::t('usuario', 'If you already registered, sign in and connect this account on settings page'), ['/user/settings/networks']), ['class' => 'text-center']);
	echo Html::endTag('div');
echo Html::endTag('div');
