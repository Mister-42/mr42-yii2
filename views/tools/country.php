<?php
use yii\bootstrap\{ActiveForm, Html};
use yii\helpers\ArrayHelper;

$this->title = 'Country Information';
$this->params['breadcrumbs'][] = 'Tools';
if ($model->load(Yii::$app->request->post())) {
	$post = Yii::$app->request->post('Country');
	$data = $model->find()->where(['ISO3166-1-Alpha-2' => $post['iso']])->one();
	$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['country']];
	$this->params['breadcrumbs'][] = $data['name'];
	$this->title .= ' - ' . $data['name'];
} else
	$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', Html::encode($this->title));

echo '<div class="site-country">';
	echo '<div class="row">';
		echo '<div class="col-md-4">';
			$form = ActiveForm::begin();
			$countries = $model->find()->select('ISO3166-1-Alpha-2, name')->orderBy('name')->all();
			echo $form->field($model, 'iso')->dropDownList(ArrayHelper::map($countries, 'ISO3166-1-Alpha-2', 'name'), [
				'onchange' => 'if(this.value!=0){this.form.submit();}',
				'options' => [
					$post['iso'] => ['selected' => true]
				],
				'prompt' => ($model->load(Yii::$app->request->post())) ? NULL : 'Select a country',
			])->label(false);
			ActiveForm::end();
		echo '</div>';
	echo '</div>';

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
		] as $item => $name) :
			if ($data[$item] === '')
				continue;
			elseif ($item === 'is_independent' && $data[$item] === 'Yes')
				continue;
			elseif ($item === 'Geoname ID')
				$data[$item] = Html::a($data[$item], 'http://geonames.org/' . $data[$item]);

			echo Html::tag('div',
				Html::tag('div', Html::tag('strong', $name), ['class' => 'col-md-8']) .
				Html::tag('div', $data[$item], ['class' => 'col-md-4'])
			, ['class' => 'row']);
		endforeach;
	}
echo '</div>';
