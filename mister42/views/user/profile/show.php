<?php
use app\models\Icon;
use app\models\user\Profile;
use yii\bootstrap4\Html;

$this->title = $profile->name ?? $profile->user->username;
$this->params['breadcrumbs'][] = 'Profile';
$this->params['breadcrumbs'][] = $this->title;

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-12']);
		echo Html::tag('h2', $this->title) ?>
		<div class="row">
			<div class="col-md-6"><?php
				if (!empty($profile->location))
					echo Icon::show('map-marker', ['class' => 'text-muted']) . ' ' . $profile->location . '<br>';
				if (!empty($profile->website))
					echo Icon::show('globe', ['class' => 'text-muted']) . ' ' . Html::a($profile->website, $profile->website);
			?></div>
			<div class="col-md-6 text-right">
				<?= '<time datetime="'.date(DATE_W3C, $profile->user->created_at).'">' . Yii::t('usuario', 'Joined on {0, date}', $profile->user->created_at) . '</time>' ?> <?= Icon::show('calendar-alt', ['class' => 'text-muted']) ?><br>
				<?php if ($profile->user->created_at != $profile->user->updated_at): ?>
					<?= '<time datetime="'.date(DATE_W3C, $profile->user->updated_at).'">' . Yii::t('usuario', 'Updated on {0, date}', $profile->user->updated_at) . '</time>' ?> <?= Icon::show('calendar-alt', ['class' => 'text-muted']) ?>
				<?php endif; ?>
			</div>
		</div>
		<hr><?php
		if (!empty($profile->bio))
			echo Profile::show($profile);
	echo Html::endTag('div');
echo Html::endTag('div');
