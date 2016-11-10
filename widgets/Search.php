<?php
namespace app\widgets;
use Yii;
use yii\base\DynamicModel;
use yii\bootstrap\{ActiveForm, Html, Widget};

class Search extends Widget {
	public function run() {
		$model = new DynamicModel(['search_term']);
		$model->addRule('search_term', 'required');
		$model->addRule('search_term', 'string', ['max' => 25]);

		$form = ActiveForm::begin(['action' => ['articles/index', 'action' => 'search'], 'method' => 'get']);

		echo $form->field($model, 'search_term', [
				'template' => '<div class="input-group input-group-sm">{input}' . Html::tag('span', Html::submitButton(Html::icon('search'), ['class' => 'btn btn-primary']), ['class' => 'input-group-btn']) . "</div>",
			])
			->label(false)
			->textInput(['class' => 'form-control', 'name' => 'q', 'placeholder' => 'Search Articles…', 'value' => Yii::$app->request->get('q')]);

		ActiveForm::end();
	}
}
