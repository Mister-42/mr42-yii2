<?php
use app\models\Icon;
use app\models\tools\PhoneticAlphabet;
use yii\bootstrap4\{ActiveForm, Alert, Html};

$this->title = 'Phonetic Alphabet Translator';
$this->params['breadcrumbs'][] = 'Tools';
$this->params['breadcrumbs'][] = $this->title;

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-12 col-lg-8 mx-auto']);
		echo Html::tag('h1', $this->title);
		echo Html::tag('div', 'This ' . $this->title . ' will phoneticise any text that you enter in the below box. Spelling alphabet, radio alphabet, or telephone alphabet is a set of words which are used to stand for the letters of an alphabet. Each word in the spelling alphabet typically replaces the name of the letter with which it starts.', ['class' => 'alert alert-info']);

		if ($flash = Yii::$app->session->getFlash('phonetic-alphabet-success'))
			echo Alert::widget(['options' => ['class' => 'alert-success'], 'body' => $flash]);

		$form = ActiveForm::begin();

		echo $form->field($model, 'text', [
				'template' => '{label}<div class="input-group">'.Icon::fieldAddon('comment').'{input}</div>{error}',
			])->textInput(['tabindex' => ++$tab]);

		echo $form->field($model, 'alphabet', [
				'template' => '{label}<div class="input-group">'.Icon::fieldAddon('th-list').'{input}</div>{error}',
			])->dropDownList(PhoneticAlphabet::getAlphabetList(), ['tabindex' => ++$tab]);

		echo $form->field($model, 'numeric')->checkBox(['tabindex' => ++$tab]);

		echo Html::tag('div',
			Html::resetButton('Reset', ['class' => 'btn btn-default ml-1', 'tabindex' => $tab+2]) .
			Html::submitButton('Send', ['class' => 'btn btn-primary ml-1', 'tabindex' => ++$tab])
		, ['class' => 'btn-toolbar float-right form-group']);

		ActiveForm::end();
	echo Html::endTag('div');
echo Html::endTag('div');
