<?php

namespace App\ServiceModule\Presenters;

use Nette,
Model,
Andweb,
App,
Nette\Application;

use Andweb\Database\Context;

class OrderEndpointPresenter extends App\Presenters\BasePresenter
{

	/**
	 * @var Andweb\Database\Context
	 */
	public $connection;


	public function __construct(Andweb\Database\Context $connection)
	{
		$this->connection = $connection;
	}


	



}