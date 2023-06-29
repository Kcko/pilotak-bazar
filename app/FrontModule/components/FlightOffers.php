<?php

namespace App\FrontModule\Components;

use Nette,
Andweb,
App\Components\VisualPaginator;

use App\FrontModule\Model\Navigation;
use App\FrontModule;
use Nette\Application\UI;


class FlightOffers extends FrontControl
{
	protected $config = [
		'template' => 'default',
		'airportId' => null,
	];

	/**
	 * @var FrontModule\Model\FlightOffers
	 */
	protected $flightOffers;

	/**
	 * @var Navigation
	 */
	protected $modelNavigation;

	/**
	 * 
	 * @var int
	 */
	protected $airportId;


	public function __construct(FrontModule\Model\FlightOffers $flightOffers, Navigation $modelNavigation)
	{
		$this->flightOffers = $flightOffers;
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
		
		$list = $this->flightOffers->getList($this->airportId);
		$this->template->list = $list['list'];
		$this->template->airplanes = $list['airplanes'];
		
		$this->render($config);
	}



}