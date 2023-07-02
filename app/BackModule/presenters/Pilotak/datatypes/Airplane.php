<?php

namespace Andweb\Datatypes;

class Airplane extends Table
{
    public function formatPersonNumber()
	{
		self::personFormatter($this->person_no);
	}

	public static function personFormatter($n)
	{
		if ($n == 1) 
			return 'osoba';

		if ($n < 5)
			return 'osoby';

		return 'osob';
	}
}