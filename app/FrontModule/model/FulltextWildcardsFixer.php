<?php

namespace App\FrontModule\Model;

use Nette,
	Andweb;

trait FulltextWildcardsFixer 
{
	


	public function wildcardsFix($string)
	{
		$SPECIAL_OPERATORS = '+-@<>()~*';
		$split = str_split($SPECIAL_OPERATORS, 1);
		
		return str_replace($split, '', $string);
	}
	
} 