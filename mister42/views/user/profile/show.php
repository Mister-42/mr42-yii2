<?php
use app\models\user\Profile;
use yii\bootstrap4\Html;

$this->title = $profile->name ?? $profile->user->username;
$this->params['breadcrumbs'][] = Yii::t('usuario', 'Profile');
$this->params['breadcrumbs'][] = $this->title;

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-12']);
		echo Html::tag('h2', $this->title);

		echo Html::beginTag('div', ['class' => 'row']);
			echo Html::beginTag('div', ['class' => 'col text-truncate']);
				if (!empty($profile->location))
					echo Yii::$app->icon->name('map-marked')->class('mr-1 text-muted icon-fw').$profile->location;
			echo Html::endTag('div');
			echo Html::beginTag('div', ['class' => 'col text-truncate text-right']);
				if (!empty($profile->location)) :
					echo Html::tag('time', Yii::t('usuario', 'Joined on {0, date}', $profile->user->created_at), ['datetime' => date(DATE_W3C, $profile->user->created_at)]);
					echo Yii::$app->icon->name('calendar-alt')->class('mr-1 text-muted icon-fw');
				endif;
			echo Html::endTag('div');

			echo Html::tag('div', null, ['class' => 'w-100']);

			echo Html::beginTag('div', ['class' => 'col text-truncate']);
				if (!empty($profile->website))
					echo Yii::$app->icon->name('globe')->class('mr-1 text-muted icon-fw').Html::a($profile->website, $profile->website);
			echo Html::endTag('div');
			echo Html::beginTag('div', ['class' => 'col text-truncate text-right']);
				if ($profile->user->created_at !== $profile->user->updated_at) :
					echo Html::tag('time', Yii::t('usuario', 'Updated on {0, date}', $profile->user->updated_at), ['datetime' => date(DATE_W3C, $profile->user->updated_at)]);
					echo Yii::$app->icon->name('calendar-alt')->class('mr-1 text-muted icon-fw');
				endif;
			echo Html::endTag('div');
		echo Html::endTag('div');

		if (!empty($profile->bio)) :
			echo Html::tag('hr');
			echo Html::tag('div', Profile::show($profile), ['class' => 'alert alert-secondary']);
		endif;

	echo Html::endTag('div');
echo Html::endTag('div');
