<?php
namespace app\models\tools;
use Yii;
use yii\helpers\ArrayHelper;

class PhoneticAlphabet extends \yii\base\Model {
	public $text;
	public $alphabet;
	public $numeric = true;

	public function rules() {
		return [
			[['text', 'alphabet'], 'required'],
			['alphabet', 'in', 'range' => self::getAlphabetList('column', 'lng')],
			['numeric', 'boolean'],
		];
	}

	public function attributeLabels() {
		return [
			'text' => 'Text to convert',
			'alphabet' => 'Phonetic Alphabet to use',
			'numeric' => 'Convert digits',
		];
	}

	public function convertText() {
		if ($this->validate()) {
			$alpha = 'alphabetArray' . $this->alphabet;
			$numeric = 'numericArray' . $this->alphabet;

			$this->text = preg_replace('/[^a-z0-9. -]+/i', '', $this->text);
			$text = strtolower($this->text);
			$text = preg_replace("/(.)/i","\${1} ", $text);
			$text = strtr($text, ArrayHelper::merge(
				self::$alpha(),
				($this->numeric) ? self::$numeric() : [],
				[
					'   ' => ' · ',
					' - ' => PHP_EOL,
				]
			));
			$text = Yii::$app->formatter->asNtext($text);
			$text = trim($text);

			return Yii::$app->getSession()->setFlash('phonetic-alphabet-success', $text);
		}
		return false;
	}

	public function getAlphabetList($type = 'map', $name = null) {
		$list = [
			['lng' => 'Icao',		'name' => 'ICAO/NATO phonetic alphabet'],
			['lng' => 'Lapd',		'name' => 'LAPD radio alphabet'],
			['lng' => 'Nlbe',		'name' => 'Dutch (Belgium)'],
			['lng' => 'Nlnl',		'name' => 'Dutch (The Netherlands)'],
			['lng' => 'De',			'name' => 'German'],
			['lng' => 'Fr',			'name' => 'French'],
			['lng' => 'It',			'name' => 'Italian'],
			['lng' => 'Es',			'name' => 'Spanish'],
			['lng' => 'Useless',	'name' => 'The Non-Phonetic Alphabet (Use at your own risk!)'],
		];

		switch ($type) {
			case 'map'		: return ArrayHelper::map($list, 'lng', 'name');
			case 'column'	: return ArrayHelper::getColumn($list, $name);
		}
	}

	private function alphabetArrayIcao() {
		return ['a' => 'Alfa',
				'b' => 'Bravo',
				'c' => 'Charlie',
				'd' => 'Delta',
				'e' => 'Echo',
				'f' => 'Foxtrot',
				'g' => 'Golf',
				'h' => 'Hotel',
				'i' => 'India',
				'j' => 'Juliett',
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
		];
	}

	private function numericArrayIcao() {
		return ['0' => 'Zero',
				'1' => 'One',
				'2' => 'Two',
				'3' => 'Three',
				'4' => 'Four',
				'5' => 'Five',
				'6' => 'Six',
				'7' => 'Seven',
				'8' => 'Eight',
				'9' => 'Nine',
		];
	}

	private function alphabetArrayLapd() {
		return ['a' => 'Adam',
				'b' => 'Boy',
				'c' => 'Charles',
				'd' => 'David',
				'e' => 'Edward',
				'f' => 'Frank',
				'g' => 'George',
				'h' => 'Henry',
				'i' => 'Ida',
				'j' => 'John',
				'k' => 'King',
				'l' => 'Lincoln',
				'm' => 'Mary',
				'n' => 'Nora',
				'o' => 'Ocean',
				'p' => 'Paul',
				'q' => 'Queen',
				'r' => 'Robert',
				's' => 'Sam',
				't' => 'Tom',
				'u' => 'Union',
				'v' => 'Victor',
				'w' => 'William',
				'x' => 'X-ray',
				'y' => 'Young',
				'z' => 'Zebra',
		];
	}

	private function numericArrayLapd() {
		return ArrayHelper::merge(
			['9' => 'Niner'],
			self::numericArrayIcao()
		);
	}

	private function alphabetArrayNlbe() {
		return ['a' => 'Arthur',
				'b' => 'Brussel',
				'c' => 'Carolina',
				'd' => 'Desiré',
				'e' => 'Emiel',
				'f' => 'Frederik',
				'g' => 'Gustaaf',
				'h' => 'Hendrik',
				'i' => 'Isidoor',
				'j' => 'Jozef',
				'k' => 'Kilogram',
				'l' => 'Leopold',
				'm' => 'Maria',
				'n' => 'Napoleon',
				'o' => 'Oscar',
				'p' => 'Piano',
				'q' => 'Quotiënt',
				'r' => 'Robert',
				's' => 'Sofie',
				't' => 'Telefoon',
				'u' => 'Ursula',
				'v' => 'Victor',
				'w' => 'Waterloo',
				'x' => 'Xavier',
				'y' => 'Yvonne',
				'z' => 'Zola',
		];
	}

	private function numericArrayNlbe() {
		return self::numericArrayNlnl();
	}

	private function alphabetArrayNlnl() {
		return ['a' => 'Anton',
				'b' => 'Bernhard',
				'c' => 'Cornelis',
				'd' => 'Dirk',
				'e' => 'Eduard',
				'f' => 'Ferdinand',
				'g' => 'Gerard',
				'h' => 'Hendrik',
				'i' => 'Isaak',
				'j' => 'Johannes',
				'k' => 'Karel',
				'l' => 'Lodewijk',
				'm' => 'Marie',
				'n' => 'Nico',
				'o' => 'Otto',
				'p' => 'Pieter',
				'q' => 'Quotiënt',
				'r' => 'Rudolf',
				's' => 'Simon',
				't' => 'Tinus',
				'u' => 'Utrecht',
				'v' => 'Victor',
				'w' => 'Willem',
				'x' => 'Xantippe',
				'y' => 'Ypsilon',
				'z' => 'Zacharias',
		];
	}

	private function numericArrayNlnl() {
		return ['0' => 'Nul',
				'1' => 'Een',
				'2' => 'Twee',
				'3' => 'Drie',
				'4' => 'Vier',
				'5' => 'Vijf',
				'6' => 'Zes',
				'7' => 'Zeven',
				'8' => 'Acht',
				'9' => 'Negen',
		];
	}

	private function alphabetArrayDe() {
		return ['a' => 'Anton',
				'b' => 'Berta',
				'c' => 'Cäsar',
				'd' => 'Dora',
				'e' => 'Emil',
				'f' => 'Friedrich',
				'g' => 'Gustav',
				'h' => 'Heinrich',
				'i' => 'Ida',
				'j' => 'Julius',
				'k' => 'Kaufmann',
				'l' => 'Ludwig',
				'm' => 'Martha',
				'n' => 'Nordpol',
				'o' => 'Otto',
				'p' => 'Paula',
				'q' => 'Quelle',
				'r' => 'Richard',
				's' => 'Samuel',
				't' => 'Theodor',
				'u' => 'Ulrich',
				'v' => 'Viktor',
				'w' => 'Wilhelm',
				'x' => 'Xanthippe',
				'y' => 'Ypsilon',
				'z' => 'Zacharias',
		];
	}

	private function numericArrayDe() {
		return ['0' => 'Null',
				'1' => 'Eins',
				'2' => 'Zwei',
				'3' => 'Drei',
				'4' => 'Vier',
				'5' => 'Fünf',
				'6' => 'Sechs',
				'7' => 'Sieben',
				'8' => 'Acht',
				'9' => 'Neun',
		];
	}

	private function alphabetArrayFr() {
		return ['a' => 'Anatole',
				'b' => 'Berthe',
				'c' => 'Célestine',
				'd' => 'Désiré',
				'e' => 'Eugène',
				'f' => 'François',
				'g' => 'Gaston',
				'h' => 'Henri',
				'i' => 'Irma',
				'j' => 'Joseph',
				'k' => 'Kléber',
				'l' => 'Louis',
				'm' => 'Marcel',
				'n' => 'Nicolas',
				'o' => 'Oscar',
				'p' => 'Pierre',
				'q' => 'Quintal',
				'r' => 'Raoul',
				's' => 'Suzanne',
				't' => 'Thérèse',
				'u' => 'Ursule',
				'v' => 'Victor',
				'w' => 'William',
				'x' => 'Xavier',
				'y' => 'Yvonne',
				'z' => 'Zoé',
		];
	}

	private function numericArrayFr() {
		return ['0' => 'Zéro',
				'1' => 'Un',
				'2' => 'Deux',
				'3' => 'Trois',
				'4' => 'Quatre',
				'5' => 'Cinq',
				'6' => 'Six',
				'7' => 'Sept',
				'8' => 'Huit',
				'9' => 'Neuf',
		];
	}

	private function alphabetArrayIt() {
		return ['a' => 'Ancona',
				'b' => 'Bari',
				'c' => 'Como',
				'd' => 'Domodossola',
				'e' => 'Empoli',
				'f' => 'Firenze',
				'g' => 'Genova',
				'h' => 'Hotel',
				'i' => 'Imola',
				'j' => 'Jolly',
				'k' => 'Kursaal',
				'l' => 'Livorno',
				'm' => 'Milano',
				'n' => 'Napoli',
				'o' => 'Otranto',
				'p' => 'Palermo',
				'q' => 'Quarto',
				'r' => 'Roma',
				's' => 'Savona',
				't' => 'Torino',
				'u' => 'Udine',
				'v' => 'Venezia',
				'w' => 'Washington',
				'x' => 'Xilofono',
				'y' => 'Yogurt',
				'z' => 'Zara',
		];
	}

	private function numericArrayIt() {
		return ['0' => 'Zero',
				'1' => 'Uno',
				'2' => 'Due',
				'3' => 'Tre',
				'4' => 'Quattro',
				'5' => 'Cinque',
				'6' => 'Sei',
				'7' => 'Sette',
				'8' => 'Otto',
				'9' => 'Nove',
		];
	}

	private function alphabetArrayEs() {
		return ['a' => 'Antonio',
				'b' => 'Barcelona',
				'c' => 'Carmen',
				'd' => 'Dolores',
				'e' => 'Enrique',
				'f' => 'Francia',
				'g' => 'Gerona',
				'h' => 'Historia',
				'i' => 'Inés',
				'j' => 'José',
				'k' => 'Kilo',
				'l' => 'Lorenzo',
				'm' => 'Madrid',
				'n' => 'Navarra',
				'o' => 'Oviedo',
				'p' => 'Paris',
				'q' => 'Querido',
				'r' => 'Ramón',
				's' => 'Sábado',
				't' => 'Tarragona',
				'u' => 'Ulises',
				'v' => 'Valencia',
				'w' => 'Wáshington',
				'x' => 'Xiqeuna',
				'y' => 'Yegua',
				'z' => 'Zaragoza',
		];
	}

	private function numericArrayEs() {
		return ['0' => 'Cero',
				'1' => 'Uno',
				'2' => 'Dos',
				'3' => 'Tres',
				'4' => 'Cuatro',
				'5' => 'Cinco',
				'6' => 'Seis',
				'7' => 'Siete',
				'8' => 'Ocho',
				'9' => 'Nueve',
		];
	}

	private function alphabetArrayUseless() {
		return ['a' => 'Are',
				'b' => 'Bee',
				'c' => 'Cue',
				'd' => 'Double-U',
				'e' => 'Eye',
				'f' => 'Five',
				'g' => 'Gnome',
				'h' => 'Hour',
				'i' => 'I',
				'j' => 'Jalapeño',
				'k' => 'Knight',
				'l' => 'Lye',
				'm' => 'Mnemonic',
				'n' => 'Nine',
				'o' => 'Owe',
				'p' => 'Pneumatic',
				'q' => 'Queue',
				'r' => 'Rest',
				's' => 'Sea',
				't' => 'Tchaikovsky',
				'u' => 'Understand?',
				'v' => 'Vie',
				'w' => 'Why',
				'x' => 'Xu',
				'y' => 'You',
				'z' => 'Zero',
		];
	}

	private function numericArrayUseless() {
		return ['0' => 'Oh',
		];
	}
}
