<?php
namespace app\models\tools\PhoneticAlphabet;
use app\models\tools\PhoneticAlphabet as Alphabet;

class DutchBe extends Alphabet
{
	public function name()
	{
		return 'Dutch (Belgium)';
	}

	public function sortOrder()
	{
		return self::name();
	}

	public function replaceArray()
	{
		return [	'a' => 'Arthur',
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
					'0' => 'Nul',
					'1' => 'Een',
					'2' => 'Twee',
					'3' => 'Drie',
					'4' => 'Vier',
					'5' => 'Vijf',
					'6' => 'Zes',
					'7' => 'Zeven',
					'8' => 'Acht',
					'9' => 'Negen',
					'-' => '<br />',
		];
	}
}
