<?php
namespace app\models\tools;
use Yii;
use yii\base\Model;

class Nato extends Model
{
	public $text;

	public function rules()
	{
		return [
			[['text'], 'required'],
		];
	}

	public function attributeLabels()
	{
		return [
			'text' => 'Text to convert',
		];
	}

	public function convertText()
	{
		if ($this->validate()) {
			$nato = [
				'a' => 'Alfa',
				'b' => 'Bravo',
				'c' => 'Charlie',
				'd' => 'Delta',
				'e' => 'Echo',
				'f' => 'Foxtrot',
				'g' => 'Golf',
				'h' => 'Hotel',
				'i' => 'India',
				'j' => 'Juliet',
				'k' => 'Kilo',
				'l' => 'Lima',
				'm' => 'Mike',
				'n' => 'November',
				'o' => 'Oscar',
				'p' => 'Papa',
				'q' => 'Quebec',
				'r' => 'Romeo',
				's' => 'Sierra',
				't' => 'Tango',
				'u' => 'Uniform',
				'v' => 'Victor',
				'w' => 'Whiskey',
				'x' => 'X-ray',
				'y' => 'Yankee',
				'z' => 'Zulu',
				'0' => 'Zero',
				'1' => 'One',
				'2' => 'Two',
				'3' => 'Three',
				'4' => 'Four',
				'5' => 'Five',
				'6' => 'Six',
				'7' => 'Seven',
				'8' => 'Eight',
				'9' => 'Nine',
				'-' => '<br />',
			];

			$this->text = preg_replace('/[^a-z0-9. -]+/i', '', $this->text);
			$text = strtolower($this->text);
			$text = preg_replace("/(.)/i","\${1} ", $text);
			$text = strtr($text, $nato);
			$text = trim($text);

			Yii::$app->getSession()->setFlash('nato-success', $text);
			return false;
		}
	}
}
