<?php
namespace app\models\tools\PhoneticAlphabet;
use app\models\tools\PhoneticAlphabet as Alphabet;

class _nlNL extends Alphabet
{
	public function name()
	{
		return 'Dutch (The Netherlands)';
	}

	public function replaceArray()
	{
		return [	'a' => 'Anton',
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
