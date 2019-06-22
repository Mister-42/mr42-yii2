<?php

use app\widgets\Lightbox;
use yii\bootstrap4\Html;

$this->title = implode(' ', [$data[0]->artist->name, 'Lyrics']);
$this->params['breadcrumbs'] = [Yii::t('mr42', 'Music')];
$this->params['breadcrumbs'][] = ['label' => Yii::t('mr42', 'Lyrics'), 'url' => ['lyrics']];
$this->params['breadcrumbs'][] = Html::tag('span', $data[0]->artist->name, ['class' => 'notranslate']);

echo Html::beginTag('div', ['class' => 'site-lyrics-albums']);
	echo Html::tag(
		'div',
		Html::tag(
			'div',
			Html::tag('h1', $data[0]->artist->name, ['class' => 'float-left']) .
			Html::tag(
				'div',
				($data[0]->artistInfo->buy
					? Html::a(Yii::$app->icon->name('bandcamp', 'brands'), $data[0]->artistInfo->buy, ['class' => 'btn btn-secondary ml-1', 'title' => Yii::t('mr42', 'Buy Music of {artist}', ['artist' => $data[0]->artist->name])])
					: '') .
				($data[0]->artistInfo->website
					? Html::a(Yii::$app->icon->name('globe'), $data[0]->artistInfo->website, ['class' => 'btn btn-secondary ml-1', 'title' => Yii::t('mr42', 'Website of {artist}', ['artist' => $data[0]->artist->name])])
					: ''),
				['class' => 'float-right']
			),
			['class' => 'col']
		),
		['class' => 'row']
	);

	if ($data[0]->artistInfo->bioSummaryParsed) {
		echo Html::tag(
			'div',
			Html::tag(
				'div',
				$data[0]->artistInfo->bioSummaryParsed,
				['class' => 'col']
			),
			['class' => 'row']
		);
	}

	foreach ($data as $album) {
		echo Html::beginTag('div', ['class' => 'row']);
		echo Html::beginTag('div', ['class' => ($album === end($data)) ? 'col mb-1' : 'col mb-3']);
		echo Html::beginTag('div', ['class' => 'card']);
		echo Html::tag(
			'div',
			Html::tag('h4', "{$album->year} · " . (
				$album->tracks
							? Html::a($album->name, ['lyrics', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url], ['class' => 'notranslate'])
							: $album->name
						), ['class' => 'float-left']) .
						Html::tag(
							'div',
							($album->buy
								? Html::a(Yii::$app->icon->name('bandcamp', 'brands')->class('mr-1') . Yii::t('mr42', 'Buy'), $album->buy, ['class' => 'btn btn-sm btn-outline-secondary ml-1', 'title' => Yii::t('mr42', 'Buy This Album')])
								: '') .
							($album->playlist_url
								? Html::a(Yii::$app->icon->name($album->playlist_source, 'brands')->class('mr-1') . Yii::t('mr42', 'Play'), $album->playlist_url, ['class' => 'btn btn-sm btn-outline-secondary ml-1'])
								: '') .
							($album->active
								? Html::a(Yii::$app->icon->name('file-pdf')->class('mr-1') . Yii::t('mr42', 'PDF'), ['albumpdf', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url], ['class' => 'btn btn-sm btn-outline-secondary ml-1'])
								: Html::tag('span', Yii::$app->icon->name('asterisk')->class('mr-1') . Yii::t('mr42', 'Draft'), ['class' => 'btn btn-sm btn-warning disabled ml-1'])),
							['class' => 'float-right']
						),
			['class' => 'card-header']
					);

		echo Html::beginTag('div', ['class' => 'container media mx-1']);
		echo Html::beginTag('div', ['class' => 'row mr-2 media-body text-truncate']);
		foreach (array_chunk($album->tracks, ceil(count($album->tracks) / 3)) as $tracks) {
			echo Html::beginTag('div', ['class' => 'col-md-4']);
			foreach ($tracks as $track) {
				echo Html::beginTag('div', ['class' => 'text-truncate notranslate']);
				$track->name = Html::a($track->name, ['lyrics', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url, '#' => $track->track]);
				echo implode(' · ', [$track->track, $track->name . $track->nameExtra]) . $track->icons;
				echo Html::endTag('div');
			}
			echo Html::endTag('div');
		}
		echo Html::endTag('div');

		if ($album->image && $album->tracks) {
			echo Lightbox::widget([
								'imageOptions' => ['style' => "background-color:{$album->image_color}"],
								'items' => [
									[
										'thumb' => ['albumcover', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url, 'size' => '100'],
										'image' => ['albumcover', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url, 'size' => '800'],
										'title' => implode(' - ', [$album->artist->name, $album->name]),
										'group' => $album->artist->url,
									],
								],
								'options' => [
									'imageFadeDuration' => 25,
									'wrapAround' => true,
								],
							]);
		}
		echo Html::endTag('div');
		echo Html::endTag('div');
		echo Html::endTag('div');
		echo Html::endTag('div');
	}
echo Html::endTag('div');
