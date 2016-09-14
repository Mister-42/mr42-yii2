<?php
namespace app\models\tools\PhoneticAlphabet;
use app\models\tools\PhoneticAlphabet as Alphabet;

class _it extends Alphabet
{
	public function name()
	{
		return 'Italian';
	}

	public function replaceArray()
	{
		return [	'a' => 'Ancona',
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
					'0' => 'zero',
					'1' => 'Uno',
					'2' => 'Due',
					'3' => 'Tre',
					'4' => 'Quattro',
					'5' => 'Cinque',
					'6' => 'Sei',
					'7' => 'Sette',
					'8' => 'Otto',
					'9' => 'Nove',
					'-' => '<br />',
		];
	}
}
