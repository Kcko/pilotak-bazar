<?php

namespace Andweb\Datatypes;

class Airplane extends Table
{
    public function formatPersonNumber()
	{
		if ($this->person_no == 1) 
			return 'osoba';

		if ($this->person_no < 5)
			return 'osoby';

		return 'osob';
	}
}