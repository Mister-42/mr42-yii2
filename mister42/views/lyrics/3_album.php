<?php
use app\models\Icon;
use yii\bootstrap4\Html;

$this->title = implode(' - ', [$data[0]->artist->name, $data[0]->album->name, 'Lyrics']);
$this->params['breadcrumbs'][] = ['label' => 'Lyrics', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $data[0]->artist->name, 'url' => ['index', 'artist' => $data[0]->artist->url]];
$this->params['breadcrumbs'][] = $data[0]->album->name;

if ($data[0]->album->image_color) :
	$this->params['themeColor'] = $data[0]->album->image_color;
endif;

echo Html::beginTag('div', ['class' => 'site-lyrics-lyrics']);
	echo Html::beginTag('div', ['class' => 'row']);
		echo Html::beginTag('div', ['class' => 'col mb-2']);
			echo Html::beginTag('div', ['class' => 'card']);
				echo Html::tag('div',
					Html::tag('div',
						Html::tag('h4', implode(' · ', [$data[0]->artist->name, $data[0]->album->name]))
					, ['class' => 'float-left']).
					Html::tag('div',
						($data[0]->album->playlist_url
							? Html::a(Icon::show('youtube', ['prefix' => 'fab fa-']).' Play', $data[0]->album->playlist_url, ['class' => 'btn btn-sm btn-light ml-1'])
							: '').
						($data[0]->album->active
							? Html::a(Icon::show('file-pdf').' PDF', ['albumpdf', 'artist' => $data[0]->artist->url, 'year' => $data[0]->album->year, 'album' => $data[0]->album->url], ['class' => 'btn btn-sm btn-light ml-1'])
							: '')
					, ['class' => 'float-right'])
				, ['class' => 'card-header']);

				echo Html::beginTag('div', ['class' => 'container mx-1']);
					echo Html::beginTag('div', ['class' => 'row mr-3']);
						$x = $y = 0;
							foreach ($data as $track) :
								if ($x++ === 0) :
									echo Html::beginTag('div', ['class' => 'col-md-4']);
								endif;

								echo Html::beginTag('div', ['class' => 'text-truncate']);
									echo $track->track.' · ';
									echo $track->lyricid || $track->video
										? Html::a($track->name, '#'.$track->track)
										: $track->name;
									echo $track->disambiguation.$track->feat;
									if ($track->video) :
										echo Icon::show($track->lyricid || $track->wip ? 'video' : 'file-video', ['class' => 'text-muted ml-1']);
									endif;
								echo Html::endTag('div');

								if (++$y === count($data) || $x === (int) ceil(count($data) / 3)) :
									echo Html::endTag('div');
									$x = 0;
								endif;
							endforeach;
					echo Html::endTag('div');
				echo Html::endTag('div');
				echo Html::tag('div',
					Html::img(['albumcover', 'artist' => $data[0]->artist->url, 'year' => $data[0]->album->year, 'album' => $data[0]->album->url, 'size' => 500], ['alt' => implode(' · ', [$data[0]->artist->name, $data[0]->album->name]), 'class' => 'img-fluid img-thumbnail rounded', 'height' => 500, 'width' => 500, 'style' => 'background-color:'.$data[0]->album->image_color])
				, ['class' => 'card-body text-center']);
			echo Html::endTag('div');
		echo Html::endTag('div');
	echo Html::endTag('div');

	foreach ($data as $track) :
		if ($track->lyricid || $track->wip || $track->video) :
			echo Html::tag('div',
				Html::tag('div',
					Html::a(null, null, ['class' => 'anchor', 'id' => $track->track]).
					Html::tag('h4', implode(' · ', [$track->track, $track->name.$track->disambiguation.$track->feat])).
					Html::tag('div', $track->wip ? Html::tag('i', 'Work in Progress') : ($track->lyricid ? $track->lyrics->lyrics : ''), ['class' => 'lyrics'])
				, ['class' => $track->lyricid || $track->wip ? 'col-12 col-md-8' : 'col-12']).
				Html::tag('div', $track->video, ['class' => $track->lyricid || $track->wip ? 'col-12 col-md-4' : 'col-12'])
			, ['class' => 'row']);
		endif;
	endforeach;
echo Html::endTag('div');
