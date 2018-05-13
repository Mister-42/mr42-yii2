<?php
use app\models\{Form, Icon};
use yii\bootstrap4\{ActiveForm, Html};
use yii\web\View;
use yii\widgets\Pjax;

$rules = $model->rules();
$this->registerJs(Yii::$app->formatter->jspack('formCharCounter.js', ['%max%' => $rules['charCount']['max']]), View::POS_READY);

Pjax::begin(['enablePushState' => false, 'linkSelector' => 'pjaxtrigger', 'options' => ['class' => 'comment-form']]);
	echo Html::tag('h3', 'Leave a Comment');

	$form = ActiveForm::begin(['id' => 'comment-form', 'options' => ['data-pjax' => '']]);

		if (Yii::$app->user->isGuest) {
			echo '<div class="row">';
				echo $form->field($model, 'name', [
					'options' => ['class' => 'col-md-6'],
					'template' => '{label}<div class="input-group">'.Icon::fieldAddon('user').'{input}</div>{error}',
				])->textInput(['tabindex' => ++$tab]);

				echo $form->field($model, 'email', [
					'options' => ['class' => 'col-md-6'],
					'template' => '{label}<div class="input-group">'.Icon::fieldAddon('envelope').'{input}</div>{hint}{error}',
				])->input('email', ['tabindex' => ++$tab])
				->hint('This will never be published.');
			echo '</div>';

			echo $form->field($model, 'website', [
				'template' => '{label}<div class="input-group">'.Icon::fieldAddon('globe').'{input}</div>{error}',
			])->input('url', ['tabindex' => ++$tab]);
		}

		echo $form->field($model, 'title', [
				'template' => '{label}<div class="input-group">'.Icon::fieldAddon('heading').'{input}</div>{error}',
			])->textInput(['tabindex' => ++$tab]);

		echo $form->field($model, 'content', [
				'template' => '{label} <div id="chars" class="float-right"></div><div class="input-group">'.Icon::fieldAddon('comment').'{input}</div> {hint} {error}'
			])
			->textarea(['id' => 'formContent', 'rows' => 6, 'tabindex' => ++$tab])
			->hint('You may use '.Html::a('Markdown Syntax', Yii::$app->params['shortDomain'].'art4', ['target' => '_blank']).'. HTML is not allowed.');

		if (Yii::$app->user->isGuest)
			echo Form::captcha($form, $model, ++$tab);

		echo Html::tag('div',
			Html::resetButton('Reset', ['class' => 'btn btn-default ml-1', 'tabindex' => $tab + 2]).
			Html::submitButton('Submit', ['class' => 'btn btn-primary ml-1', 'id' => 'pjaxtrigger', 'tabindex' => ++$tab])
		, ['class' => 'btn-toolbar float-right form-group']);

	ActiveForm::end();
Pjax::end();
