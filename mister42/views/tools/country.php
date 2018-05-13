<?php
use app\models\Icon;
use app\models\tools\Country;
use yii\bootstrap4\{ActiveForm, Html};
use yii\helpers\ArrayHelper;

$title = 'Country Information';
$this->params['breadcrumbs'][] = 'Tools';
$this->params['breadcrumbs'][] = Yii::$app->request->isPost ? ['label' => $title, 'url' => ['country']] : $title;

$model = new Country;

if ($model->load(Yii::$app->request->post())) {
	$post = Yii::$app->request->post('Country');
	$model->iso = $post['iso'];
	$data = $model->find()->where(['ISO3166-1-Alpha-2' => $post['iso']])->one();
	$this->params['breadcrumbs'][] = $data['official_name_en'];
}
$this->title = $model->load(Yii::$app->request->post()) ? implode(' - ', [$data['official_name_en'], $title]) : $title;

echo Html::tag('h1', $model->load(Yii::$app->request->post() ? implode(' - ', [$title, $data['official_name_en']]) : $title));

echo Html::beginTag('div', ['class' => 'site-country']);
	echo Html::beginTag('div', ['class' => 'row']);
		echo Html::beginTag('div', ['class' => 'col-md-4']);
			$form = ActiveForm::begin();

			$countries = $model->find()->select('ISO3166-1-Alpha-2, official_name_en')->orderBy('official_name_en')->all();
			echo $form->field($model, 'iso', [
				'template' => '{label}<div class="input-group">'.Icon::fieldAddon('th-list').'{input}</div>{error}',
			])->dropDownList(ArrayHelper::map($countries, 'ISO3166-1-Alpha-2', 'official_name_en'), [
				'onchange' => 'if(this.value!=0){this.form.submit();}',
				'prompt' => $model->load(Yii::$app->request->post()) ? null : 'Select a country',
			])->label(false);

			ActiveForm::end();
		echo Html::endTag('div');
	echo Html::endTag('div');

	if ($model->load(Yii::$app->request->post())) {
		foreach (require(Yii::getAlias('@app/data/countryMapping.php')) as $item => $name) :
			if (empty($data->$item))
				$data->$item = Html::tag('span', 'unknown', ['class' => 'text-muted']);

			echo Html::tag('div',
				Html::tag('div', $name, ['class' => 'col-md-8']).
				Html::tag('div', $data->$item, ['class' => 'col-md-4'])
			, ['class' => 'row']);
		endforeach;
	}
echo Html::endTag('div');
