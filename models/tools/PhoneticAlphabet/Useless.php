<?php
namespace app\models\tools\PhoneticAlphabet;
use app\models\tools\PhoneticAlphabet as Alphabet;

class Useless extends Alphabet
{
	public function name()
	{
		return 'The Non-Phonetic Alphabet (Use at your own risk!)';
	}

	public function sortOrder()
	{
		return 'Z';
	}

	public function replaceArray()
	{
		return [	'a' => 'Are',
					'b' => 'Bee',
					'c' => 'Cue',
					'd' => 'Double-U',
					'e' => 'Eye',
					'f' => 'Five',
					'g' => 'Gnome',
					'h' => 'Hour',
					'i' => 'I',
					'j' => 'Jalapeno',
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
					'0' => 'Oh',
		];
	}
}
