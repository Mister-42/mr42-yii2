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
	$this->params['breadcrumbs'][] = $data['official_name_en'];
}
$this->title = $model->load(Yii::$app->request->post()) ? implode(' - ', [$data['official_name_en'], $title]) : $title;

echo Html::tag('h1', Html::encode($model->load(Yii::$app->request->post()) ? implode(' - ', [$title, $data['official_name_en']]) : $title));

echo Html::beginTag('div', ['class' => 'site-country']);
	echo Html::beginTag('div', ['class' => 'row']);
		echo Html::beginTag('div', ['class' => 'col-md-4']);
			$form = ActiveForm::begin();

			$countries = $model->find()->select('ISO3166-1-Alpha-2, official_name_en')->orderBy('official_name_en')->all();
			echo $form->field($model, 'iso', [
				'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('th-list').'</span>{input}</div>{error}',
			])->dropDownList(ArrayHelper::map($countries, 'ISO3166-1-Alpha-2', 'official_name_en'), [
				'onchange' => 'if(this.value!=0){this.form.submit();}',
				'prompt' => $model->load(Yii::$app->request->post()) ? null : 'Select a country',
			])->label(false);

			ActiveForm::end();
		echo Html::endTag('div');
	echo Html::endTag('div');

	if ($model->load(Yii::$app->request->post())) {
		foreach ([
			'official_name_ar'							=> 'Country or Area official Arabic short name from UN Statistics Divsion',
			'official_name_cn'							=> 'Country or Area official Chinese short name from UN Statistics Divsion',
			'official_name_en'							=> 'Country or Area official English short name from UN Statistics Divsion',
			'official_name_es'							=> 'Country or Area official Spanish short name from UN Statistics Divsion',
			'official_name_fr'							=> 'Country or Area official French short name from UN Statistics Divsion',
			'official_name_ru'							=> 'Country or Area official Russian short name from UN Statistics Divsion',
			'ISO3166-1-Alpha-2'							=> 'Alpha-2 codes from ISO 3166-1',
			'ISO3166-1-Alpha-3'							=> 'Alpha-3 codes from ISO 3166-1 (synonymous with World Bank Codes)',
			'ISO3166-1-numeric'							=> 'Numeric codes from ISO 3166-1',
			'ISO4217-currency_alphabetic_code'			=> 'ISO 4217 currency alphabetic code',
			'ISO4217-currency_country_name'				=> 'ISO 4217 country name',
			'ISO4217-currency_country_name'				=> 'ISO 4217 country name',
			'ISO4217-currency_minor_unit'				=> 'ISO 4217 currency number of minor units',
			'ISO4217-currency_name'						=> 'ISO 4217 currency name',
			'ISO4217-currency_numeric_code'				=> 'ISO 4217 currency numeric code',
			'M49'										=> 'UN Statistics M49 numeric codes',
			'UNTERM Arabic Formal'						=> 'Country\'s formal Arabic name from UN Protocol and Liaison Service',
			'UNTERM Arabic Short'						=> 'Country\'s short Arabic name from UN Protocol and Liaison Service',
			'UNTERM Chinese Formal'						=> 'Country\'s formal Chinese name from UN Protocol and Liaison Service',
			'UNTERM Chinese Short'						=> 'Country\'s short Chinese name from UN Protocol and Liaison Service',
			'UNTERM English Formal'						=> 'Country\'s formal English name from UN Protocol and Liaison Service',
			'UNTERM English Short'						=> 'Country\'s short English name from UN Protocol and Liaison Service',
			'UNTERM French Formal'						=> 'Country\'s formal French name from UN Protocol and Liaison Service',
			'UNTERM French Short'						=> 'Country\'s short French name from UN Protocol and Liaison Service',
			'UNTERM Russian Formal'						=> 'Country\'s formal Russian name from UN Protocol and Liaison Service',
			'UNTERM Russian Short'						=> 'Country\'s short Russian name from UN Protocol and Liaison Service',
			'UNTERM Spanish Formal'						=> 'Country\'s formal Spanish name from UN Protocol and Liaison Service',
			'UNTERM Spanish Short'						=> 'Country\'s short Spanish name from UN Protocol and Liaison Service',
			'CLDR display name'							=> 'Country\'s customary English short name (CLDR)',
			'Capital'									=> 'Capital city from Geonames',
			'Continent'									=> 'Continent from Geonames',
			'DS'										=> 'Distinguishing signs of vehicles in international traffic',
			'Developed / Developing Countries'			=> 'Country classification from United Nations Statistics Division',
			'Dial'										=> 'Country code from ITU-T recommendation E.164, sometimes followed by area code',
			'EDGAR'										=> 'EDGAR country code from SEC',
			'FIFA'										=> 'Codes assigned by the Fédération Internationale de Football Association',
			'FIPS'										=> 'Codes from the U.S. standard FIPS PUB 10-4',
			'GAUL'										=> 'Global Administrative Unit Layers from the Food and Agriculture Organization',
			'Geoname ID'								=> 'Geoname ID',
			'Global Code'								=> 'Country classification from United Nations Statistics Division',
			'Global Name'								=> 'Country classification from United Nations Statistics Division',
			'IOC'										=> 'Codes assigned by the International Olympics Committee',
			'ITU'										=> 'Codes assigned by the International Telecommunications Union',
			'Intermediate Region Code'					=> 'Country classification from United Nations Statistics Division',
			'Intermediate Region Name'					=> 'Country classification from United Nations Statistics Division',
			'Land Locked Developing Countries (LLDC)'	=> 'Country classification from United Nations Statistics Division',
			'Languages'									=> 'Languages from Geonames',
			'Least Developed Countries (LDC)'			=> 'Country classification from United Nations Statistics Division',
			'MARC'										=> 'MAchine-Readable Cataloging codes from the Library of Congress',
			'Region Code'								=> 'Country classification from United Nations Statistics Division',
			'Region Name'								=> 'Country classification from United Nations Statistics Division',
			'Small Island Developing States (SIDS)'		=> 'Country classification from United Nations Statistics Division',
			'Sub-region Code'							=> 'Country classification from United Nations Statistics Division',
			'Sub-region Name'							=> 'Country classification from United Nations Statistics Division',
			'TLD'										=> 'Top level domain from Geonames',
			'WMO'										=> 'Country abbreviations by the World Meteorological Organization',
			'is_independent'							=> 'Country status, based on the CIA World Factbook',
			'source'									=> 'Source',
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
