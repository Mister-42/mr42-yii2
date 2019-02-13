<?php
use app\models\Form;
use himiklab\yii2\recaptcha\ReCaptcha;
use yii\bootstrap4\{ActiveForm, Html};
use yii\widgets\Pjax;

Form::charCount($this, $model->rules()['charCount']['max']);

Pjax::begin(['enablePushState' => false, 'linkSelector' => 'pjaxtrigger', 'options' => ['class' => 'comment-form']]);
	echo Html::tag('h3', Yii::t('mr42', 'Leave a Comment'));

	$form = ActiveForm::begin(['id' => 'comment-form', 'options' => ['data-pjax' => '']]);
		$tab = 0;

		if (Yii::$app->user->isGuest) :
			echo '<div class="row">';
				echo $form->field($model, 'nameField', [
					'options' => ['class' => 'col-md-6'],
					'template' => '{label}<div class="input-group">'.Yii::$app->icon->fieldAddon('user').'{input}</div>{error}',
				])->textInput(['tabindex' => ++$tab]);

				echo $form->field($model, 'emailField', [
					'options' => ['class' => 'col-md-6'],
					'template' => '{label}<div class="input-group">'.Yii::$app->icon->fieldAddon('envelope').'{input}</div>{hint}{error}',
				])->input('email', ['tabindex' => ++$tab])
				->hint(Yii::t('mr42', 'This will never be published.'));
			echo '</div>';

			echo $form->field($model, 'website', [
				'template' => '{label}<div class="input-group">'.Yii::$app->icon->fieldAddon('globe').'{input}</div>{error}',
			])->input('url', ['tabindex' => ++$tab]);
		endif;

		echo $form->field($model, 'title', [
				'template' => '{label}<div class="input-group">'.Yii::$app->icon->fieldAddon('heading').'{input}</div>{error}',
			])->textInput(['tabindex' => ++$tab]);

		echo $form->field($model, 'content', [
				'template' => '{label} <div id="chars" class="float-right"></div><div class="input-group">'.Yii::$app->icon->fieldAddon('comment').'{input}</div> {hint} {error}'
			])
			->textarea(['id' => 'formContent', 'rows' => 6, 'tabindex' => ++$tab])
			->hint(Yii::t('mr42', 'You may use {markdown}. HTML is not allowed.', ['markdown' => Html::a(Yii::t('mr42', 'Markdown Syntax'), ['/permalink/articles', 'id' => 4], ['target' => '_blank'])]));

		if (Yii::$app->user->isGuest)
			echo $form->field($model, 'captcha')->widget(ReCaptcha::className())->label(false);

		echo Html::tag('div',
			Html::resetButton(Yii::t('mr42', 'Reset'), ['class' => 'btn btn-default ml-1', 'tabindex' => $tab + 2]).
			Html::submitButton(Yii::t('mr42', 'Submit'), ['class' => 'btn btn-primary ml-1', 'id' => 'pjaxtrigger', 'tabindex' => ++$tab])
		, ['class' => 'btn-toolbar float-right form-group']);

	ActiveForm::end();
Pjax::end();
