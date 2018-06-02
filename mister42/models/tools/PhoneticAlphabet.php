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
			['alphabet', 'in', 'range' => static::getAlphabetList('lng')],
			['numeric', 'boolean'],
		];
	}

	public function attributeLabels(): array {
		return [
			'text' => Yii::t('mr42', 'Text to convert'),
			'alphabet' => Yii::t('mr42', 'Phonetic Alphabet to use'),
			'numeric' => Yii::t('mr42', 'Convert Digits'),
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
			static::getAlpha($this->alphabet),
			($this->numeric) ? static::getNumeric($this->alphabet) : [],
			static::getGenericReplacements()
		));
		$text = Yii::$app->formatter->asNtext($text);
		$text = trim($text);

		Yii::$app->getSession()->setFlash('phonetic-alphabet-success', $text);
		return true;
	}

	public static function getAlphabetList(string $column = '*'): array {
		$name = 'name_'.Yii::$app->language;
		$list = self::find()
			->select("lng, {$name} AS name")
			->orderBy('sort, name')
			->all();

		if ($column !== '*') :
			return ArrayHelper::getColumn($list, $column);
		endif;
		return ArrayHelper::map($list, 'lng', 'name');
	}

	private static function getAlpha(string $language): array {
		return self::find()
			->where(['lng' => $language])
			->asArray()
			->one();
	}

	private static function getNumeric(string $language): array {
		return PhoneticAlphabetNumeric::find()
			->where(['lng' => $language])
			->asArray()
			->one();
	}

	private static function getGenericReplacements(): array {
		return ['   ' => ' · ',
				' - ' => PHP_EOL,
		];
	}
}
