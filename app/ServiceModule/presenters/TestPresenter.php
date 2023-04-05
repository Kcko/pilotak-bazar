<?php

namespace App\ServiceModule\Presenters;

use Nette,
Model,
Andweb,
App,
Nette\Application;

use Andweb\Database\Context;

class TestPresenter extends App\Presenters\BasePresenter
{

	/**
	 * @var Andweb\Database\Context
	 */
	public $connection;


	public function __construct(Andweb\Database\Context $connection)
	{
		$this->connection = $connection;
	}



	public function beforeRender()
	{
		parent::beforeRender();

		$this->getTemplate()->addFilter('toJs', function ($s) {

			$s = (string) str_replace(array("\r", "\r\n", "\n"), '', $s);
			$s = str_replace("'", "\"", $s);

			return $s;
		});
	}



}