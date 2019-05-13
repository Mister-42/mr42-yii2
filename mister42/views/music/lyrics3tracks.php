<?php
use app\widgets\Lightbox;
use yii\bootstrap4\Html;
use yii\helpers\Url;

$this->title = implode(' - ', [$data[0]->artist->name, $data[0]->album->name]);
$this->title = implode(' ', [$this->title, 'Lyrics']);
$this->params['breadcrumbs'] = [Yii::t('mr42', 'Music')];
$this->params['breadcrumbs'][] = ['label' => Yii::t('mr42', 'Lyrics'), 'url' => ['lyrics']];
$this->params['breadcrumbs'][] = ['label' => Html::tag('span', $data[0]->artist->name, ['class' => 'notranslate']), 'url' => ['lyrics', 'artist' => $data[0]->artist->url]];
$this->params['breadcrumbs'][] = Html::tag('span', $data[0]->album->name, ['class' => 'notranslate']);

if ($data[0]->album->image)
	$this->registerMetaTag(['property' => 'og:image', 'content' => Url::to(['albumcover', 'artist' => $data[0]->artist->url, 'year' => $data[0]->album->year, 'album' => $data[0]->album->url, 'size' => 800], true)]);
$this->registerMetaTag(['property' => 'og:type', 'content' => 'music.album']);
$this->registerLinkTag(['rel' => 'alternate', 'href' => Url::to(['albumpdf', 'artist' => $data[0]->artist->url, 'year' => $data[0]->album->year, 'album' => $data[0]->album->url], true), 'type' => 'application/pdf', 'title' => 'PDF']);

if ($data[0]->album->image_color)
	$this->params['themeColor'] = $data[0]->album->image_color;

echo Html::beginTag('div', ['class' => 'site-lyrics-lyrics']);
	echo Html::beginTag('div', ['class' => 'row']);
		echo Html::beginTag('div', ['class' => 'col mb-2']);
			echo Html::beginTag('div', ['class' => 'card']);
				echo Html::tag('div',
					Html::tag('div',
						Html::tag('h4', implode(' · ', [$data[0]->artist->name, $data[0]->album->name]), ['class' => 'notranslate'])
					, ['class' => 'float-left']).
					Html::tag('div',
						($data[0]->album->buy
							? Html::a(Yii::$app->icon->show('bandcamp', ['class' => 'mr-1', 'style' => 'brands']).Yii::t('mr42', 'Buy'), $data[0]->album->buy, ['class' => 'btn btn-sm btn-outline-secondary ml-1', 'title' => Yii::t('mr42', 'Buy This Album')])
							: '').
						($data[0]->album->playlist_url
							? Html::a(Yii::$app->icon->show($data[0]->album->playlist_source, ['class' => 'mr-1', 'style' => 'brands']).Yii::t('mr42', 'Play'), $data[0]->album->playlist_url, ['class' => 'btn btn-sm btn-outline-secondary ml-1'])
							: '').
						($data[0]->album->active
							? Html::a(Yii::$app->icon->show('file-pdf', ['class' => 'mr-1']).Yii::t('mr42', 'PDF'), ['albumpdf', 'artist' => $data[0]->artist->url, 'year' => $data[0]->album->year, 'album' => $data[0]->album->url], ['class' => 'btn btn-sm btn-outline-secondary ml-1'])
							: Html::tag('span', Yii::$app->icon->show('asterisk', ['class' => 'mr-1']).Yii::t('mr42', 'Draft'), ['class' => 'btn btn-sm btn-warning disabled ml-1']))
					, ['class' => 'float-right'])
				, ['class' => 'card-header']);

				echo Html::beginTag('div', ['class' => 'container mx-1']);
					echo Html::beginTag('div', ['class' => 'row mr-3']);
						$x = 0;
						foreach ($data as $track) :
							if ($x === 0 || $x % ceil(count($data) / 3) === 0)
								echo Html::beginTag('div', ['class' => 'col-md-4']);

							echo Html::beginTag('div', ['class' => 'text-truncate notranslate']);
								echo $track->track.' · ';
								echo $track->lyricid || $track->video
									? Html::a($track->name, '#'.$track->track)
									: $track->name;
								echo $track->disambiguation.$track->feat;
								if ($track->video)
									echo Yii::$app->icon->show($track->video_source, ['class' => 'text-muted ml-1', 'style' => 'brands']);
							echo Html::endTag('div');

							if (++$x === count($data) || $x % ceil(count($data) / 3) === 0)
								echo Html::endTag('div');
						endforeach;
					echo Html::endTag('div');
				echo Html::endTag('div');
				if ($data[0]->album->image)
					echo Lightbox::widget([
						'imageOptions' => ['class' => 'img-fluid img-thumbnail rounded', 'style' => "background-color:{$data[0]->album->image_color}"],
						'items' => [
							[
								'thumb'	=> ['albumcover', 'artist' => $data[0]->artist->url, 'year' => $data[0]->album->year, 'album' => $data[0]->album->url, 'size' => '500'],
								'image'	=> ['albumcover', 'artist' => $data[0]->artist->url, 'year' => $data[0]->album->year, 'album' => $data[0]->album->url, 'size' => '800'],
								'title'	=> implode(' - ', [$data[0]->artist->name, $data[0]->album->name]),
							],
						],
						'linkOptions' => ['class' => 'card-body text-center'],
						'options' => [
							'imageFadeDuration'	=> 25,
							'wrapAround'		=> true,
						],
					]);
			echo Html::endTag('div');
		echo Html::endTag('div');
	echo Html::endTag('div');

	foreach ($data as $track) :
		if ($track->lyricid || $track->wip || $track->video) :
			echo Html::beginTag('div', ['class' => 'row']);
				echo Html::tag('div',
					Html::tag('h4', implode(' · ', [$track->track, $track->name.$track->disambiguation.$track->feat]), ['class' => 'notranslate'])
				, ['class' => $track->lyricid || $track->wip ? 'col-12 col-md-8' : 'col-12']);
				echo Html::tag('div', $track->video, ['class' => $track->lyricid || $track->wip ? 'col-12 col-md-4 order-md-12' : 'col-12']);
				echo Html::tag('div',
					Html::tag('span', null, ['class' => 'anchor', 'id' => $track->track]).
					Html::tag('div', $track->wip ? Html::tag('i', 'Work in Progress') : ($track->lyricid ? $track->lyrics->lyrics : ''), ['class' => 'lyrics notranslate'])
				, ['class' => $track->lyricid || $track->wip ? 'col-12 col-md-8' : 'col-12']);
			echo Html::endTag('div');
		endif;
	endforeach;
echo Html::endTag('div');
