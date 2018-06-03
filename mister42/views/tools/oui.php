<?php
use app\models\Form;
use app\models\tools\Oui;
use yii\bootstrap4\{ActiveForm, Alert, Html};

$this->title = Yii::t('mr42', 'OUI Lookup');
$this->params['breadcrumbs'][] = Yii::t('mr42', 'Tools');
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', $this->title);

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-12 col-lg-8 mx-auto']);
		echo Html::beginTag('div', ['class' => 'alert alert-info']);
			echo Html::tag('div', Yii::t('mr42', 'This {oui} Lookup tool provides an easy way to look up OUIs and other MAC address prefixes. Type or paste in a OUI, MAC address, or name below.', ['oui' => Html::tag('abbr', 'OUI', ['title' => Yii::t('mr42', 'Organizationally Unique Identifier')])]));
		echo Html::endTag('div');

		if (Yii::$app->request->post()) :
			$post = Yii::$app->request->post('Oui');
			$mac = substr(preg_replace('/[^a-f0-9 ]/i', '', strtolower($post['oui'])), 0, 6);
			$data = Oui::find()
					->select(['Assignment', 'Organization_Name'])
					->where(['Assignment' => $mac])
					->orWhere(['LIKE', 'Organization_Name', $post['oui']])
					->all();
			Alert::begin(['options' => ['class' => 'alert-success fade show clearfix']]);
				foreach ($data as $item) :
					echo Html::tag('div',
						Html::tag('div', wordwrap($item->Assignment, 2, ':', true), ['class' => 'col-2']).
						Html::tag('div', $item->Organization_Name, ['class' => 'col-10'])
					, ['class' => 'row']);
				endforeach;
				if (count($data) === 0) :
					echo Html::tag('div', Yii::t('yii', 'No results found.'));
				endif;
			Alert::end();
		endif;

		$form = ActiveForm::begin();
		$tab = 0;

		echo $form->field($model, 'oui', [
				'template' => '{label}<div class="input-group">'.Yii::$app->icon->fieldAddon('desktop').'{input}</div>{error}',
			])->textInput(['tabindex' => ++$tab]);

		echo Form::submitToolbar(Yii::t('mr42', 'Submit'), $tab);

		ActiveForm::end();
	echo Html::endTag('div');
echo Html::endTag('div');
