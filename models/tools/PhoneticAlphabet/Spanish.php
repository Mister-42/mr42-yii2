<?php
namespace app\models\tools\PhoneticAlphabet;
use app\models\tools\PhoneticAlphabet as Alphabet;

class Spanish extends Alphabet
{
	public function name()
	{
		return 'Spanish';
	}

	public function sortOrder()
	{
		return self::name();
	}

	public function replaceArray()
	{
		return [	'a' => 'Antonio',
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
					'0' => 'Cero',
					'1' => 'Uno',
					'2' => 'Dos',
					'3' => 'Tres',
					'4' => 'Cuatro',
					'5' => 'Cinco',
					'6' => 'Seis',
					'7' => 'Siete',
					'8' => 'Ocho',
					'9' => 'Nueve',
					'-' => '<br />',
		];
	}
}
