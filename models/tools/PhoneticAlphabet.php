<?php
namespace app\models\tools;
use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\helpers\ArrayHelper;

class PhoneticAlphabet extends Model
{
	public $text;
	public $alphabet;

	public function rules()
	{
		return [
			[['text', 'alphabet'], 'required'],
			['alphabet', 'in', 'range' => self::listAlphabets('column', 'file')],
		];
	}

	public function attributeLabels()
	{
		return [
			'text' => 'Text to convert',
			'alphabet' => 'Phonetic Alphabet to use',
		];
	}

	public function convertText()
	{
		if ($this->validate()) {
			$className = 'app\\models\\tools\\PhoneticAlphabet\\' . $this->alphabet;
			$alphabet = new $className();

			$this->text = preg_replace('/[^a-z0-9. -]+/i', '', $this->text);
			$text = strtolower($this->text);
			$text = preg_replace("/(.)/i","\${1} ", $text);
			$text = strtr($text, $alphabet->replaceArray());
			$text = trim($text);

			return Yii::$app->getSession()->setFlash('phonetic-alphabet-success', $text);
		}
		return false;
	}

	public function listAlphabets($type = 'map', $name = null)
	{
		$alphabetFiles = FileHelper::findFiles(__DIR__ . '/PhoneticAlphabet/', ['only'=>['*.php'], 'recursive' => false]);
		sort($alphabetFiles);

		foreach ($alphabetFiles as $file) {
			$file = basename($file, '.php');
			$className = 'app\\models\\tools\\PhoneticAlphabet\\' . $file;
			$alphabet = new $className();
			
			$alphabetOptions[] = ['file' => $file, 'name' => $alphabet->name()];
		}

		switch ($type) {
			case 'map'		:	return ArrayHelper::map($alphabetOptions, 'file', 'name');
			case 'column'	:	return ArrayHelper::getColumn($alphabetOptions, $name);
		}
	}
}
