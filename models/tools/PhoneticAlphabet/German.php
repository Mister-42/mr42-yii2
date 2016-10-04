<?php
namespace app\models\tools\PhoneticAlphabet;

class German extends \app\models\tools\PhoneticAlphabet
{
	public function name()
	{
		return 'German';
	}

	public function sortOrder()
	{
		return self::name();
	}

	public function replaceArray()
	{
		return [	'a' => 'Anton',
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
					'0' => 'Null',
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
}
