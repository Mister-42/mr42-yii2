<?php
use app\models\Icon;
use app\widgets\Lightbox;
use yii\bootstrap4\Html;

$this->title = implode(' - ', [$data[0]->artist->name, 'Lyrics']);
$this->params['breadcrumbs'][] = ['label' => 'Lyrics', 'url' => ['index']];
$this->params['breadcrumbs'][] = $data[0]->artist->name;

echo Html::beginTag('div', ['class' => 'site-lyrics-albums']);
	echo Html::tag('div',
		Html::tag('div',
			Html::tag('h1', $data[0]->artist->name, ['class' => 'float-left']).
			Html::tag('div',
				($data[0]->artist->buy
					? Html::a(Icon::show('bandcamp', ['prefix' => 'fab fa-']), $data[0]->artist->buy, ['class' => 'btn btn-secondary ml-1', 'title' => 'Buy music of '.$data[0]->artist->name])
					: '').
				($data[0]->artist->website
					? Html::a(Icon::show('globe'), $data[0]->artist->website, ['class' => 'btn btn-secondary ml-1', 'title' => 'Website of '.$data[0]->artist->name])
					: '')
			, ['class' => 'float-right'])
		, ['class' => 'col'])
	, ['class' => 'row']);

	foreach ($data as $album) :
		echo Html::beginTag('div', ['class' => 'row']);
			echo Html::beginTag('div', ['class' => ' col mb-2']);
				echo Html::beginTag('div', ['class' => 'card']);
					echo Html::tag('div',
						Html::tag('h4', "{$album->year} · ".((Yii::$app->user->identity->isAdmin || $album->active) && $album->tracks
							? Html::a($album->name, ['index', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url])
							: $album->name
						), ['class' => 'float-left']).
						Html::tag('div',
							($album->playlist_url
								? Html::a(Icon::show('youtube', ['prefix' => 'fab fa-']).' Play', $album->playlist_url, ['class' => 'btn btn-sm btn-light ml-1'])
								: '').
							($album->active
								? Html::a(Icon::show('file-pdf').' PDF', ['albumpdf', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url], ['class' => 'btn btn-sm btn-light ml-1'])
								: '')
						, ['class' => 'float-right'])
					, ['class' => 'card-header']);

					echo Html::beginTag('div', ['class' => 'container media mx-1']);
						echo Html::beginTag('div', ['class' => 'row mr-2 media-body']);
							$x = $y = 0;
							foreach ($album->tracks as $track) :
								if ($x++ === 0) :
									echo Html::beginTag('div', ['class' => 'col-md-4']);
								endif;

								echo Html::beginTag('div', ['class' => 'text-truncate']);
									$track->name = (Yii::$app->user->identity->isAdmin || $album->active) && ($track->lyricid || $track->video)
										? Html::a($track->name, ['index', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url, '#' => $track->track])
										: $track->name;
									echo implode(' · ', [$track->track, $track->name]).$track->disambiguation.$track->feat;
									if ((Yii::$app->user->identity->isAdmin || $album->active) && $track->video) :
										echo Icon::show($track->lyricid || $track->wip ? 'video' : 'file-video', ['class' => 'text-muted ml-1']);
									endif;
								echo Html::endTag('div');

								if (++$y === count($album->tracks) || $x === (int) ceil(count($album->tracks) / 3)) :
									echo Html::endTag('div');
									$x = 0;
								endif;
							endforeach;
						echo Html::endTag('div');

						if ($album->image && count($album->tracks) > 0) :
							echo Lightbox::widget([
								'imageOptions' => ['style' => 'background-color:'.$album->image_color],
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
						endif;
					echo Html::endTag('div');
				echo Html::endTag('div');
			echo Html::endTag('div');
		echo Html::endTag('div');
	endforeach;
echo Html::endTag('div');
