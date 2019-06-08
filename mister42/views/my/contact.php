<?php
use app\assets\{CharCounterAsset, InputFileAsset};
use himiklab\yii2\recaptcha\ReCaptcha;
use yii\bootstrap4\{ActiveForm, Html};
use yii\web\View;
use yii\widgets\Pjax;

$this->title = Yii::t('mr42', 'Contact');
$this->params['breadcrumbs'] = [Yii::$app->name];
$this->params['breadcrumbs'][] = $this->title;

CharCounterAsset::register($this, $model->rules()['charCount']['max']);
InputFileAsset::register($this);

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
					'inputTemplate' => Yii::$app->icon->inputTemplate('user'),
				])->textInput(['tabindex' => ++$tab]);

				echo $form->field($model, 'email', [
					'options' => ['class' => 'col-md-6 form-group'],
					'inputTemplate' => Yii::$app->icon->inputTemplate('at'),
				])->input('email', ['tabindex' => ++$tab]);
			echo '</div>';

			echo $form->field($model, 'title', [
				'inputTemplate' => Yii::$app->icon->inputTemplate('heading'),
			])->textInput(['tabindex' => ++$tab]);

			echo $form->field($model, 'content', [
				'inputTemplate' => '<div id="chars" class="float-right"></div>'.Yii::$app->icon->inputTemplate('comment'),
			])->textarea(['id' => 'formContent', 'rows' => 6, 'tabindex' => ++$tab]);

			echo $form->field($model, 'attachment', [
				'inputTemplate' => '<div class="input-group">'.Yii::$app->icon->fieldAddon('paperclip').Html::tag('div', '{input}'.Html::tag('label', Yii::t('mr42', 'Select a File'), ['class' => 'custom-file-label text-truncate']), ['class' => 'custom-file']).'</div>',
			])->fileInput(['class' => 'custom-file-input', 'id' => 'sourceFile', 'tabindex' => ++$tab]);

			echo $form->field($model, 'captcha')->widget(ReCaptcha::class)->label(false);

			echo Html::tag('div',
				Html::resetButton(Yii::t('mr42', 'Reset'), ['class' => 'btn btn-default ml-1', 'tabindex' => $tab + 2]).
				Html::submitButton(Yii::t('mr42', 'Send'), ['class' => 'btn btn-primary ml-1', 'id' => 'pjaxtrigger', 'tabindex' => ++$tab])
			, ['class' => 'btn-toolbar float-right form-group']);

			ActiveForm::end();
		Pjax::end();
	echo Html::endTag('div');
echo Html::endTag('div');
