<?php

namespace App\FrontModule\Model;

use Nette,
	Andweb;

trait HtmlCleaner 
{
	
	private function clean($s)
	{
		$s = preg_replace('/style=\\"[^\\"]*\\"/', '', $s);
		
		return $s;
	}

} 