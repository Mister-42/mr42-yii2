<?php
use app\assets\CharCounterAsset;
use app\models\ActiveForm;
use himiklab\yii2\recaptcha\ReCaptcha;
use yii\bootstrap4\Html;
use yii\widgets\Pjax;

CharCounterAsset::register($this, $model->rules()['charCount']['max']);

Pjax::begin(['enablePushState' => false, 'linkSelector' => 'pjaxtrigger', 'options' => ['class' => 'comment-form']]);
	echo Html::tag('h3', Yii::t('mr42', 'Leave a Comment'), ['class' => 'text-center']);

	$form = ActiveForm::begin(['action' => ['newcomment', 'id' => Yii::$app->request->get('id')], 'id' => 'comment-form', 'options' => ['data-pjax' => '']]);
		$tab = 0;

		if (Yii::$app->user->isGuest) :
			echo '<div class="row">';
				echo $form->field($model, 'name', [
					'icon' => 'user',
					'options' => ['class' => 'col-md-6'],
				])->textInput(['tabindex' => ++$tab]);

				echo $form->field($model, 'email', [
					'icon' => 'envelope',
					'options' => ['class' => 'col-md-6'],
				])->input('email', ['tabindex' => ++$tab])
				->hint(Yii::t('mr42', 'This will never be published.'));
			echo '</div>';

			echo $form->field($model, 'website', [
				'icon' => 'globe',
			])->input('url', ['tabindex' => ++$tab]);
		endif;

		echo $form->field($model, 'title', [
			'icon' => 'heading',
		])->textInput(['tabindex' => ++$tab]);

		echo $form->field($model, 'content', [
			'inputTemplate' => Yii::$app->icon->name('comment')->activeFieldAddon(),
		])->textarea(['id' => 'formContent', 'rows' => 6, 'tabindex' => ++$tab])
		->hint(Yii::t('mr42', 'You may use {markdown}. HTML is not allowed.', ['markdown' => Html::a(Yii::t('mr42', 'Markdown Syntax'), Yii::$app->urlManagerMr42->createUrl(['/permalink/articles', 'id' => 4]), ['target' => '_blank'])]));

		if (Yii::$app->user->isGuest)
			echo $form->field($model, 'captcha')->widget(ReCaptcha::class)->label(false);

		echo Html::tag('div',
			Html::resetButton(Yii::t('mr42', 'Reset'), ['class' => 'btn btn-default ml-1', 'tabindex' => $tab + 2]).
			Html::submitButton(Yii::t('mr42', 'Submit'), ['class' => 'btn btn-primary ml-1', 'id' => 'pjaxtrigger', 'tabindex' => ++$tab])
		, ['class' => 'btn-toolbar float-right form-group']);

	ActiveForm::end();
Pjax::end();
