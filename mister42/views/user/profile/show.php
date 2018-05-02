<?php
use app\models\Icon;
use app\models\user\Profile;
use yii\bootstrap4\Html;

$this->title = $profile->name ?? $profile->user->username;
$this->params['breadcrumbs'][] = 'Profile';
$this->params['breadcrumbs'][] = $this->title;

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-12']);
		echo Html::tag('h2', $this->title);

		echo Html::beginTag('div', ['class' => 'row']);
			echo Html::beginTag('div', ['class' => 'col text-truncate']);
				if (!empty($profile->location))
					echo Icon::show('map-marker', ['class' => 'text-muted mr-1']) . $profile->location;
			echo Html::endTag('div');
			echo Html::beginTag('div', ['class' => 'col text-truncate text-right']);
				if (!empty($profile->location)) {
					echo Html::tag('time', Yii::t('usuario', 'Joined on {0, date}', $profile->user->created_at), ['datetime' => date(DATE_W3C, $profile->user->created_at)]);
					echo Icon::show('calendar-alt', ['class' => 'text-muted ml-1']);
				}
			echo Html::endTag('div');

			echo Html::tag('div', null, ['class' => 'w-100']);

			echo Html::beginTag('div', ['class' => 'col text-truncate']);
				if (!empty($profile->website))
					echo Icon::show('globe', ['class' => 'text-muted mr-1']) . Html::a($profile->website, $profile->website);
			echo Html::endTag('div');
			echo Html::beginTag('div', ['class' => 'col text-truncate text-right']);
				if ($profile->user->created_at != $profile->user->updated_at) {
					echo Html::tag('time', Yii::t('usuario', 'Updated on {0, date}', $profile->user->updated_at), ['datetime' => date(DATE_W3C, $profile->user->updated_at)]);
					echo Icon::show('calendar-alt', ['class' => 'text-muted ml-1']);
				}
			echo Html::endTag('div');
		echo Html::endTag('div');

		echo Html::tag('hr');

		if (!empty($profile->bio))
			echo Html::tag('div', Profile::show($profile), ['class' => 'alert alert-secondary']);

	echo Html::endTag('div');
echo Html::endTag('div');
