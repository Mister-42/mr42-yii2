<?php
namespace app\models\tools\PhoneticAlphabet;

class French extends \app\models\tools\PhoneticAlphabet
{
	public function name()
	{
		return 'French';
	}

	public function sortOrder()
	{
		return self::name();
	}

	public function replaceArray()
	{
		return [	'a' => 'Anatole',
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
					'0' => 'Zéro',
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
}
