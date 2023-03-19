<?php

namespace App\FrontModule\Components;

use Nette,
App\Model;

class Online extends Nette\Application\UI\Control
{
	public $onRender;


	public function render()
	{

		$x = 1034;
		$this->onRender($this, $x);
		$this->template->setFile(__DIR__ . '/../templates/components/Online/default.latte');
		$this->template->render();
	}



}