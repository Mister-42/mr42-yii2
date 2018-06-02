<?php
use app\models\Form;
use yii\bootstrap4\{ActiveForm, Html};
use yii\web\View;
use yii\widgets\Pjax;

$this->title = Yii::t('mr42', 'Contact');
$this->params['breadcrumbs'][] = $this->title;

Form::charCount($this, $model->rules()['charCount']['max']);
$this->registerJs("var inputFile = {lang:{selected:'".Yii::t('mr42', 'File {name} Selected', ['name' => Html::tag('span', null, ['class' => 'filename'])])."'}};".Yii::$app->formatter->jspack('inputFile.js'), View::POS_READY);

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-12 col-lg-8 mx-auto']);
		echo Html::tag('h1', $this->title);

		Pjax::begin(['enablePushState' => false, 'linkSelector' => 'pjaxtrigger']);
			echo Html::tag('div', Yii::t('mr42', 'If you have inquiries or other questions, please fill out the following form to contact {siteName}. Thank you.', ['siteName' => Yii::$app->name]), ['class' => 'alert alert-info']);

			$form = ActiveForm::begin(['options' => ['data-pjax' => '']]);
			$tab = 0;

			echo '<div class="row">';
				echo $form->field($model, 'name', [
					'options' => ['class' => 'col-md-6 form-group'],
					'template' => '{label}<div class="input-group">'.Yii::$app->icon->fieldAddon('user').'{input}</div>{error}',
				])->textInput(['tabindex' => ++$tab]);

				echo $form->field($model, 'email', [
					'options' => ['class' => 'col-md-6 form-group'],
					'template' => '{label}<div class="input-group">'.Yii::$app->icon->fieldAddon('at').'{input}</div>{error}',
				])->input('email', ['tabindex' => ++$tab]);
			echo '</div>';

			echo $form->field($model, 'title', [
					'template' => '{label}<div class="input-group">'.Yii::$app->icon->fieldAddon('heading').'{input}</div>{error}',
				])->textInput(['tabindex' => ++$tab]);

			echo $form->field($model, 'content', [
				'template' => '{label} <div id="chars" class="float-right"></div><div class="input-group">'.Yii::$app->icon->fieldAddon('comment').'{input}</div> {hint} {error}',
			])->textarea(['id' => 'formContent', 'rows' => 6, 'tabindex' => ++$tab]);

			echo $form->field($model, 'attachment', [
				'template' => Html::tag('label', $model->getAttributeLabel('attachment'), ['for' => 'sourceFile']).'<div class="input-group">'.Yii::$app->icon->fieldAddon('paperclip').'<div class="custom-file">{input}{label}</div></div>{hint} {error}',
			])->fileInput(['class' => 'custom-file-input', 'id' => 'sourceFile', 'tabindex' => ++$tab])
			->label(Yii::t('mr42', 'Select a File'), ['class' => 'custom-file-label text-truncate']);

			echo Form::captcha($form, $model, ++$tab);

			echo Html::tag('div',
				Html::resetButton(Yii::t('mr42', 'Reset'), ['class' => 'btn btn-default ml-1', 'tabindex' => $tab + 2]).
				Html::submitButton(Yii::t('mr42', 'Send'), ['class' => 'btn btn-primary ml-1', 'id' => 'pjaxtrigger', 'tabindex' => ++$tab])
			, ['class' => 'btn-toolbar float-right form-group']);

			ActiveForm::end();
		Pjax::end();
	echo Html::endTag('div');
echo Html::endTag('div');
