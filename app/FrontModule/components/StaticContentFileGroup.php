<?php

namespace App\FrontModule\Components;

use Nette,
	Andweb,
	Model,
	Andweb\Mail\Message;

class StaticContentFileGroup extends FrontControl 
{
	public function renderDefault(array $config = array())
    {		
        $this->render($config);
    }
}