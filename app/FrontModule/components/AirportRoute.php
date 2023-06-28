<?php

namespace App\FrontModule\Components;

use Nette,
Andweb,
App\Components\VisualPaginator;

use App\FrontModule\Model\Navigation;
use App\FrontModule;
use Nette\Application\UI;


class AirportRoute extends FrontControl
{
	protected $config = [
		'template' => 'default',
		'airportId' => null,
	];

	/**
	 * @var FrontModule\Model\AirportRoute
	 */
	protected $airportRoute;

	/**
	 * @var Navigation
	 */
	protected $modelNavigation;

	/**
	 * 
	 * @var int
	 */
	protected $airportId;


	public function __construct(FrontModule\Model\AirportRoute $airportRoute, Navigation $modelNavigation)
	{
		$this->airportRoute = $airportRoute;
		$this->modelNavigation = $modelNavigation;
		parent::__construct();
	}

	public function getCurrentConfig(array $config = [], $lastConfig = FALSE)
	{
		$config = parent::getCurrentConfig($config);

		return $config;
	}

	public function setAirport($airportId) 
	{
		$this->airportId = $airportId;
	}

	public function renderDefault(array $config = [])
	{
		$config = $this->getCurrentConfig($config);

		$this->template->list = $this->airportRoute->getList($config['airportId']);
		
		$this->render($config);
	}



}