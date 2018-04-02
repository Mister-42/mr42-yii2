<?php
use app\widgets\Lightbox;
use yii\bootstrap\Html;

$this->title = implode(' - ', [$albums[0]->artist->name, 'Lyrics']);
$this->params['breadcrumbs'][] = ['label' => 'Lyrics', 'url' => ['index']];
$this->params['breadcrumbs'][] = $albums[0]->artist->name;

echo '<div class="site-lyrics-albums">';
	echo Html::tag('div',
		Html::tag('div',
			Html::tag('h1', Html::encode($albums[0]->artist->name), ['class' => 'pull-left']) .
			Html::tag('div', $albums[0]->artist->website
				? Html::a(Html::icon('globe'), $albums[0]->artist->website, ['class' => 'btn btn-xs btn-warning action', 'title' => 'Website of ' . Html::encode($albums[0]->artist->name)])
				: 	''
			, ['class' => 'pull-right'])
		, ['class' => 'clearfix col-lg-12'])
	, ['class' => 'row']);

	foreach ($albums as $album) :
		echo Html::tag('div',
			Html::tag('div',
				Html::tag('div', Html::tag('h3', "{$album->year} · " . ((Yii::$app->user->identity->isAdmin || $album->active) && $album->tracks
					? Html::a($album->name, ['index', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url])
					: $album->name
				)), ['class' => 'pull-left']) .
				Html::tag('div', $album->active
					? Html::a(Html::icon('save').' PDF', ['albumpdf', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url], ['class' => 'btn btn-xs btn-warning action'])
					: ''
				, ['class' => 'pull-right']) .
				Html::tag('div', $album->playlist_url
					? Html::a(Html::icon('play').' Play', $album->playlist_url, ['class' => 'btn btn-xs btn-warning action']) . '&nbsp;'
					: ''
				, ['class' => 'pull-right'])
			, ['class' => 'clearfix col-lg-12'])
		, ['class' => 'row']);

		echo '<div class="row"><div class="col-md-12 media">';
		echo '<div class="media-body"><div class="row">';
		$x = $y = 0;
		foreach ($album->tracks as $track) :
			$y++;
			if ($x++ === 0)
				echo '<div class="col-sm-4 text-nowrap">';

			$track->name = (Yii::$app->user->identity->isAdmin || $album->active) && ($track->lyricid || $track->video)
				? Html::a($track->name, ['index', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url, '#' => $track->track])
				: $track->name;
			echo implode(' · ', [$track->track, $track->name]) . $track->disambiguation . $track->feat;
			if ((Yii::$app->user->identity->isAdmin || $album->active) && $track->video)
				echo ' ' . Html::icon($track->lyricid || $track->wip ? 'facetime-video' : 'fullscreen', ['class' => 'text-muted']);
			echo '<br>';

			if ($x === (int) ceil(count($album->tracks) / 3) || $y === count($album->tracks)) {
				echo '</div>';
				$x = 0;
			}
		endforeach;
		echo '</div></div>';
		if ($album->image && count($album->tracks) > 0)
			echo Lightbox::widget([
				'items' => [
					[
						'thumb'	=> ['albumcover', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url, 'size' => '100'],
						'image'	=> ['albumcover', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url, 'size' => '800'],
						'title'	=> implode(' · ', [$album->artist->name, $album->name]),
						'group'	=> $album->artist->url,
					],
				],
				'options' => [
					'imageFadeDuration'	=> 25,
					'wrapAround'		=> true,
				],
			]);
		echo '</div></div>';
	endforeach;
echo '</div>';
