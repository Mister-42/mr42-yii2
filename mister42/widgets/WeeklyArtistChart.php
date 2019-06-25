<?php

namespace app\widgets;

use app\models\user\User;
use app\models\user\WeeklyArtist;
use Yii;
use yii\bootstrap4\Html;
use yii\bootstrap4\Widget;

class WeeklyArtistChart extends Widget
{
    public $limit = 10;
    public $profile;

    public function run(): string
    {
        $user = User::findOne(['username' => $this->profile]);
        $items = WeeklyArtist::find()
            ->orderBy(['rank' => SORT_ASC])
            ->where(['userid' => $user->id])
            ->limit($this->limit)
            ->all();

        foreach ($items as $item) {
            $feed[] = Html::tag(
                'li',
                Html::tag('span', $item['artist'], ['class' => 'float-left']) .
                Html::tag('span', $item['count'], ['class' => 'float-right text-right']),
                ['class' => 'list-group-item text-truncate']
            );
        }

        return (!isset($feed))
            ? Html::tag('div', 'No Items to Display.', ['class' => 'ml-2'])
            : Html::tag('ul', implode($feed), ['class' => 'list-group list-group-flush']);
    }
}
