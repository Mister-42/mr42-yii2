<?php
namespace app\widgets;
use Yii;
use yii\base\DynamicModel;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\bootstrap\Widget;

class Search extends Widget
{
	public function run()
	{
		$model = new DynamicModel(['search_term']);
		$model->addRule('search_term', 'required');
		$model->addRule('search_term', 'string', ['max' => 50]);

		$form = ActiveForm::begin(['action' => ['post/index', 'action' => 'search'], 'method' => 'get']);

		$submit = Html::tag('span', Html::submitButton('Go!', ['class' => 'btn btn-primary']), ['class' => 'input-group-btn']);
		echo $form->field($model, 'search_term', [
				'template' => '<div class="input-group input-group-sm"><span class="input-group-addon">'.Html::icon('search').'</span>{input}' . $submit . "</div>",
			])
			->label(false)
			->textInput(['class' => 'form-control', 'name' => 'q', 'placeholder' => 'Search...', 'value' => Yii::$app->request->get('q')])
		;

		ActiveForm::end();
	}
}
