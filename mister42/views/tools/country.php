<?php
use yii\bootstrap\{ActiveForm, Html};
use yii\helpers\ArrayHelper;

$title = 'Country Information';
$this->params['breadcrumbs'][] = 'Tools';
$this->params['breadcrumbs'][] = Yii::$app->request->isPost ? ['label' => $title, 'url' => ['country']] : $title;

if ($model->load(Yii::$app->request->post())) {
	$post = Yii::$app->request->post('Country');
	$model->iso = $post['iso'];
	$data = $model->find()->where(['ISO3166-1-Alpha-2' => $post['iso']])->one();
	$this->params['breadcrumbs'][] = $data['name'];
}
$this->title = $model->load(Yii::$app->request->post()) ? implode(' - ', [$data['name'], $title]) : $title;

echo Html::tag('h1', Html::encode($model->load(Yii::$app->request->post()) ? implode(' - ', [$title, $data['name']]) : $title));

echo Html::beginTag('div', ['class' => 'site-country']);
	echo Html::beginTag('div', ['class' => 'row']);
		echo Html::beginTag('div', ['class' => 'col-md-4']);
			$form = ActiveForm::begin();

			$countries = $model->find()->select('ISO3166-1-Alpha-2, name')->orderBy('name')->all();
			echo $form->field($model, 'iso', [
				'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('th-list').'</span>{input}</div>{error}',
			])->dropDownList(ArrayHelper::map($countries, 'ISO3166-1-Alpha-2', 'name'), [
				'onchange' => 'if(this.value!=0){this.form.submit();}',
				'prompt' => $model->load(Yii::$app->request->post()) ? null : 'Select a country',
			])->label(false);

			ActiveForm::end();
		echo Html::endTag('div');
	echo Html::endTag('div');

	if ($model->load(Yii::$app->request->post())) {
		foreach ([
			'name'								=> 'Customary English short name (CLDR)',
			'official_name_en'					=> 'Official English short name',
			'official_name_fr'					=> 'Official French short name',
			'ISO3166-1-Alpha-2'					=> 'Alpha-2 codes from ISO 3166-1',
			'ISO3166-1-Alpha-3'					=> 'Alpha-3 codes from ISO 3166-1 (synonymous with World Bank Codes)',
			'M49'								=> 'UN Statistics M49 numeric codes',
			'ITU'								=> 'International Telecommunications Union code',
			'MARC'								=> 'MAchine-Readable Cataloging codes from the Library of Congress',
			'WMO'								=> 'Country abbreviations by the World Meteorological Organization',
			'DS'								=> 'Distinguishing signs of vehicles in international traffic',
			'Dial'								=> 'Country code from ITU-T recommendation E.164, sometimes followed by area code',
			'FIFA'								=> 'Codes assigned by the Fédération Internationale de Football Association',
			'FIPS'								=> 'Codes from the U.S. standard FIPS PUB 10-4',
			'GAUL'								=> 'Global Administrative Unit Layers from the Food and Agriculture Organization',
			'IOC'								=> 'Codes assigned by the International Olympics Committee',
			'ISO4217-currency_alphabetic_code'	=> 'ISO 4217 currency alphabetic code',
			'ISO4217-currency_country_name'		=> 'ISO 4217 country name',
			'ISO4217-currency_minor_unit'		=> 'ISO 4217 currency number of minor units',
			'ISO4217-currency_name'				=> 'ISO 4217 currency name',
			'ISO4217-currency_numeric_code'		=> 'ISO 4217 currency numeric code',
			'is_independent'					=> 'Country status, based on the CIA World Factbook',
			'Capital'							=> 'Capital city',
			'Continent'							=> 'Continent',
			'TLD'								=> 'Top level domain',
			'Languages'							=> 'Languages',
			'Geoname ID'						=> 'Geoname ID',
			'EDGAR'								=> 'EDGAR country code from SEC',
			'source'							=> 'Source',
		] as $item => $name) :
			if ($data[$item] === '')
				continue;
			elseif ($item === 'is_independent' && $data[$item] === 'Yes')
				continue;
			elseif ($item === 'Geoname ID')
				$data[$item] = Html::a($data[$item], 'http://geonames.org/' . $data[$item]);

			echo Html::tag('div',
				Html::tag('div', $name, ['class' => 'col-md-8']) .
				Html::tag('div', $data[$item], ['class' => 'col-md-4'])
			, ['class' => 'row']);
		endforeach;
	}
echo Html::endTag('div');
