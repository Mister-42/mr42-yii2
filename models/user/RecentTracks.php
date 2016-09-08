<?php
namespace app\models\user;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;

class RecentTracks extends ActiveRecord
{
	public static function tableName()
	{
		 return '{{%recenttracks}}';
	}

	public static function display($userid)
	{
		$limit = (isset(Yii::$app->params['recentTracksCount']) && is_int(Yii::$app->params['recentTracksCount'])) ? Yii::$app->params['recentTracksCount'] : 25;

		$recentTracks = RecentTracks::find()
			->where(['userid' => $userid])
			->orderBy('count DESC')
			->limit($limit)
			->all();

		foreach ($recentTracks as $track) {
			echo '<div class="clearfix track">';
				echo Html::tag('span', $track['artist'], ['class' => 'pull-left']);
				if ($track['time'] === 0)
					echo Html::tag('span', '', ['class' => 'glyphicon glyphicon-volume-up', 'title' => 'Currently playing']);
				echo Html::tag('span', $track['track'], ['class' => 'pull-right text-right']);
			echo '</div>';
		}

		echo (empty($recentTracks)) ?
			Html::tag('p', 'No items to display.') :
			Html::tag('div',
				Html::tag('span', Html::tag('strong', 'Total tracks played:'), ['class' => 'pull-left']) .
				Html::tag('span', Html::tag('strong', number_format($recentTracks[0]['count'])), ['class' => 'pull-right'])
			, ['class' => 'clearfix']);
	}
}
