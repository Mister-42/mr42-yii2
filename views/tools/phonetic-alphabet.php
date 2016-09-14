<?php
use app\models\tools\PhoneticAlphabet;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;

$this->title = 'Phonetic Alphabet Translator';
$this->params['breadcrumbs'][] = 'Tools';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-md-offset-2 col-md-8">
		<?php echo Html::tag('h1', Html::encode($this->title));

		if ($flash = Yii::$app->session->getFlash('phonetic-alphabet-success')) {
			echo Alert::widget(['options' => ['class' => 'alert-success'],'body' => $flash]);
		}

		$form = ActiveForm::begin();

		echo $form->field($model, 'text', [
				'template' => '{label}<div class="input-group"><span class="input-group-addon"><span class="glyphicon glyphicon-comment"></span></span>{input}</div>{error}',
			])->textInput(['tabindex' => 1]);

		echo $form->field($model, 'alphabet')->dropDownList(PhoneticAlphabet::listAlphabets(), ['tabindex' => 2]);
		
		echo '<div class="form-group text-right">';
		echo Html::resetButton('Reset', ['class' => 'btn btn-default', 'tabindex' => 4]) . ' ';
		echo Html::submitButton('Send', ['class' => 'btn btn-primary', 'tabindex' => 3]);
		echo '</div>';

		ActiveForm::end(); ?>
	</div>
</div>
