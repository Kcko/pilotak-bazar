<?php

namespace App\FrontModule\Components;

use Nette,
Andweb,
App\Components\VisualPaginator;

use App\FrontModule\Model\Navigation;
use App\FrontModule;
use Nette\Application\UI;


class FlightContent extends FrontControl
{
	protected $config = [
		'template' => 'default',
	];

	/**
	 * @var FrontModule\Model\FlightContent
	 */
	protected $flightContent;

	/**
	 * @var Navigation
	 */
	protected $modelNavigation;


	public function __construct(FrontModule\Model\FlightContent $flightContent, Navigation $modelNavigation)
	{
		$this->flightContent = $flightContent;
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

		$this->template->list = $this->flightContent->getList();
		
		$this->render($config);
	}



}