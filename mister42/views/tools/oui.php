<?php

use mister42\models\ActiveForm;
use mister42\models\tools\Oui;
use yii\bootstrap4\Alert;
use yii\bootstrap4\Html;

$this->title = Yii::t('mr42', 'OUI Lookup');
$this->params['breadcrumbs'][] = Yii::t('mr42', 'Tools');
$this->params['breadcrumbs'][] = $this->title;

echo Html::beginTag('div', ['class' => 'row']);
    echo Html::beginTag('div', ['class' => 'col-md-12 col-lg-8 mx-auto']);
        echo Html::tag('h1', Yii::t('mr42', '{oui} Lookup', ['oui' => Html::tag('abbr', 'OUI', ['title' => Yii::t('mr42', 'Organizationally Unique Identifier')])]));
        echo Html::beginTag('div', ['class' => 'alert alert-info shadow']);
            echo Html::tag('div', Yii::t('mr42', 'This OUI Lookup tool provides an easy way to look up OUIs and other MAC address prefixes. Type or paste in a OUI, MAC address, or name below.'));
        echo Html::endTag('div');

        if ($post = Yii::$app->request->post('Oui')) {
            $data = Oui::find()
                    ->select(['assignment', 'name'])
                    ->where(['like', 'assignment', mb_substr(preg_replace('/[^A-F0-9]+/i', '', mb_strtoupper($post['oui'])), 0, 6) . '%', false])
                    ->orWhere(['like', 'name', $post['oui']]);
            $count = (int) $data->count();

            Alert::begin(['options' => ['class' => ($count === 0) ? 'alert-danger fade show' : 'alert-success shadow fade show']]);
            foreach ($data->all() as $item) {
                echo Html::tag(
                    'div',
                    Html::tag('div', wordwrap($item->assignment, 2, ':', true), ['class' => 'col-2']) .
                        Html::tag('div', $item->name, ['class' => 'col-10']),
                    ['class' => 'row']
                );
            }
            if ($count === 0) {
                echo Html::tag('div', Yii::t('yii', 'No results found.'));
            }
            Alert::end();
        }

        $form = ActiveForm::begin();
        $tab = 0;

        echo $form->field($model, 'oui', [
            'icon' => 'desktop',
        ])->textInput(['tabindex' => ++$tab]);

        echo $form->submitToolbar(Yii::t('mr42', 'Submit'), $tab);

        ActiveForm::end();
    echo Html::endTag('div');
echo Html::endTag('div');
