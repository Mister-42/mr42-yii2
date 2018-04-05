<?php
use app\models\tools\PhoneticAlphabet;
use yii\bootstrap\{ActiveForm, Alert, Html};

$this->title = 'Phonetic Alphabet Translator';
$this->params['breadcrumbs'][] = 'Tools';
$this->params['breadcrumbs'][] = $this->title;

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-offset-2 col-md-8']);
		echo Html::tag('h1', Html::encode($this->title));
		echo Html::tag('div', 'This ' . $this->title .  ' will phoneticise any text that you enter in the below box. Spelling alphabet, radio alphabet, or telephone alphabet is a set of words which are used to stand for the letters of an alphabet. Each word in the spelling alphabet typically replaces the name of the letter with which it starts.', ['class' => 'alert alert-info']);

		if ($flash = Yii::$app->session->getFlash('phonetic-alphabet-success'))
			echo Alert::widget(['options' => ['class' => 'alert-success'], 'body' => $flash]);

		$form = ActiveForm::begin();

		echo $form->field($model, 'text', [
				'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('comment').'</span>{input}</div>{error}',
			])->textInput(['tabindex' => 1]);

		echo $form->field($model, 'alphabet')->dropDownList(PhoneticAlphabet::getAlphabetList(), ['tabindex' => 2]);

		echo $form->field($model, 'numeric')->checkbox(['tabindex' => 3]);

		echo Html::tag('div',
			Html::resetButton('Reset', ['class' => 'btn btn-default', 'tabindex' => 5]) .
			Html::submitButton('Send', ['class' => 'btn btn-primary', 'tabindex' => 4])
		, ['class' => 'btn-toolbar form-group pull-right']);

		ActiveForm::end();
	echo Html::endTag('div');
echo Html::endTag('div');
