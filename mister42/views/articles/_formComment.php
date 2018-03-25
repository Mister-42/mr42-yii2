<?php
use yii\bootstrap\{ActiveForm, Html};
use yii\captcha\Captcha;
use yii\web\View;
use yii\widgets\Pjax;

$rules = $model->rules();
$this->registerJs(Yii::$app->formatter->jspack('formCharCounter.js', ['%max%' => $rules['charCount']['max']]), View::POS_READY);

Pjax::begin(['enablePushState' => false, 'linkSelector' => 'pjaxtrigger', 'options' => ['class' => 'comment-form']]);
	echo Html::tag('h2', 'Leave a Comment');

	$form = ActiveForm::begin(['id' => 'comment-form', 'options' => ['data-pjax' => '']]);

		if (Yii::$app->user->isGuest) {
			echo '<div class="row">';
				echo $form->field($model, 'name', [
					'options' => ['class' => 'col-xs-6'],
					'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('user').'</span>{input}</div>{error}',
				])->textInput(['tabindex' => 1]);

				echo $form->field($model, 'email', [
					'options' => ['class' => 'col-xs-6'],
					'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('envelope').'</span>{input}</div>{hint}{error}',
				])->input('email', ['tabindex' => 2])
				->hint('This will not be published.');
			echo '</div>';

			echo $form->field($model, 'website', [
				'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('globe').'</span>{input}</div>{error}',
			])->input('url', ['tabindex' => 3]);
		}

		echo $form->field($model, 'title', [
				'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('header').'</span>{input}</div>{error}',
			])->textInput(['tabindex' => 4]);

		echo $form->field($model, 'content', [
				'template' => '{label} <div id="chars" class="pull-right"></div><div class="input-group"><span class="input-group-addon">'.Html::icon('comment').'</span>{input}</div> {hint} {error}'
			])
			->textarea(['id' => 'formContent', 'rows' => 6, 'tabindex' => 5])
			->hint('You may use ' . Html::a('Markdown Syntax', ['/articles/index', 'id' => 4], ['target' => '_blank']) . '. HTML is not allowed.');

		if (Yii::$app->user->isGuest) {
			echo $form->field($model, 'captcha')->widget(Captcha::class, [
				'imageOptions' => ['alt' => 'CAPTCHA image', 'class' => 'captcha'],
				'options' => ['class' => 'form-control', 'tabindex' => 6],
				'template' => '<div class="row"><div class="col-xs-3"><div class="input-group"><span class="input-group-addon">'.Html::icon('dashboard').'</span>{input}</div></div> {image}</div>',
			])->hint('Click on the image to retrieve a new verification code.');
		}

		echo Html::tag('div',
			Html::resetButton('Reset', ['class' => 'btn btn-default', 'tabindex' => 8]) . ' ' .
			Html::submitButton('Submit', ['class' => 'btn btn-primary', 'id' => 'pjaxtrigger', 'tabindex' => 7])
		, ['class' => 'form-group text-right']);

	ActiveForm::end();
Pjax::end();
