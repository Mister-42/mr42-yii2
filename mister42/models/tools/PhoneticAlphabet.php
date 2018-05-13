<?php
namespace app\models\tools;
use Yii;
use yii\helpers\ArrayHelper;

class PhoneticAlphabet extends \yii\db\ActiveRecord {
	public $text;
	public $alphabet;
	public $numeric = true;

	public static function tableName(): string {
		return 'x_phonetic_alpha';
	}

	public function rules(): array {
		return [
			[['text', 'alphabet'], 'required'],
			['alphabet', 'in', 'range' => self::getAlphabetList('lng')],
			['numeric', 'boolean'],
		];
	}

	public function attributeLabels(): array {
		return [
			'text' => 'Text to convert',
			'alphabet' => 'Phonetic Alphabet to use',
			'numeric' => 'Convert digits',
		];
	}

	public function convertText(): bool {
		if (!$this->validate()) :
			return false;
		endif;

		$this->text = preg_replace('/[^a-z0-9. -]+/i', '', $this->text);
		$text = strtolower($this->text);
		$text = preg_replace("/(.)/i", "\${1} ", $text);
		$text = strtr($text, ArrayHelper::merge(
			self::getAlpha($this->alphabet),
			($this->numeric) ? self::getNumeric($this->alphabet) : [],
			self::getGenericReplacements()
		));
		$text = Yii::$app->formatter->asNtext($text);
		$text = trim($text);

		Yii::$app->getSession()->setFlash('phonetic-alphabet-success', $text);
		return true;
	}

	public function getAlphabetList(string $column = '*'): array {
		$list = self::find()
			->select('lng, name')
			->orderBy('sort, name')
			->all();

		if ($column !== '*') :
			return ArrayHelper::getColumn($list, $column);
		endif;
		return ArrayHelper::map($list, 'lng', 'name');
	}

	private function getAlpha(string $language): array {
		return self::find()
			->where(['lng' => $language])
			->asArray()
			->one();
	}

	private function getNumeric(string $language): array {
		return PhoneticAlphabetNumeric::find()
			->where(['lng' => $language])
			->asArray()
			->one();
	}

	private function getGenericReplacements(): array {
		return ['   ' => ' · ',
				' - ' => PHP_EOL,
		];
	}
}
