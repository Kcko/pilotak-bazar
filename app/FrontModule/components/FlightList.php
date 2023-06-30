<?php

namespace App\FrontModule\Components;

use Nette,
Andweb,
App\Components\VisualPaginator;

use App\FrontModule\Model\Navigation;
use App\FrontModule;
use Nette\Application\UI;


class FlightList extends FrontControl
{
	protected $config = [
		'template' => 'default',
	];

	/**
	 * @var FrontModule\Model\FlightList
	 */
	protected $flightList;

	/**
	 * @var Navigation
	 */
	protected $modelNavigation;


	public function __construct(FrontModule\Model\FlightList $flightList, Navigation $modelNavigation)
	{
		$this->flightList = $flightList;
		$this->modelNavigation = $modelNavigation;
		parent::__construct();
	}

	public function getCurrentConfig(array $config = [], $lastConfig = FALSE)
	{
		$config = parent::getCurrentConfig($config);

		return $config;
	}

	public function renderDefault(array $config = [])
	{
		$config = $this->getCurrentConfig($config);

		$this->template->list = $this->flightList->getList();
		
		$this->render($config);
	}



}