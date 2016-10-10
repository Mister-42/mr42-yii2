<?php
namespace app\models\tools\PhoneticAlphabet;

class Italian extends \app\models\tools\PhoneticAlphabet
{
	public function name() {
		return 'Italian';
	}

	public function sortOrder() {
		return self::name();
	}

	public function replaceArray() {
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
				'0' => 'Zero',
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
}
