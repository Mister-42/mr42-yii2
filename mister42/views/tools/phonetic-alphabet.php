<?php
use app\models\Form;
use app\models\tools\PhoneticAlphabet;
use yii\bootstrap4\{ActiveForm, Alert, Html};

$this->title = Yii::t('mr42', 'Phonetic Alphabet Translator');
$this->params['breadcrumbs'][] = Yii::t('mr42', 'Tools');
$this->params['breadcrumbs'][] = $this->title;

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-12 col-lg-8 mx-auto']);
		echo Html::tag('h1', $this->title);
		echo Html::tag('div', Yii::t('mr42', 'This {title} will phoneticise any text that you enter in the below box. Spelling alphabet, radio alphabet, or telephone alphabet is a set of words which are used to stand for the letters of an alphabet. Each word in the spelling alphabet typically replaces the name of the letter with which it starts.', ['title' => $this->title]), ['class' => 'alert alert-info']);

		if ($flash = Yii::$app->session->getFlash('phonetic-alphabet-success')) :
			echo Alert::widget(['options' => ['class' => 'alert-success'], 'body' => $flash]);
		endif;

		$form = ActiveForm::begin();
		$tab = 0;

		echo $form->field($model, 'text', [
				'inputTemplate' => Yii::$app->icon->inputTemplate('comment'),
			])->textInput(['tabindex' => ++$tab]);

		echo $form->field($model, 'alphabet', [
				'inputTemplate' => Yii::$app->icon->inputTemplate('th-list'),
			])->dropDownList(PhoneticAlphabet::getAlphabetList(), ['tabindex' => ++$tab]);

		echo $form->field($model, 'numeric')->checkBox(['tabindex' => ++$tab]);

		echo Form::submitToolbar(Yii::t('mr42', 'Convert'), $tab);

		ActiveForm::end();
	echo Html::endTag('div');
echo Html::endTag('div');
