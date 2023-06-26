<?php

namespace App\FrontModule\Components;

use Nette,
Andweb,
App\Components\VisualPaginator;

use App\FrontModule\Model\Navigation;
use App\FrontModule;
use Nette\Application\UI;


class Airport extends FrontControl
{
	protected $config = [
		'template' => 'default',
	];

	/**
	 * @var FrontModule\Model\Airport
	 */
	protected $airport;

	/**
	 * @var Navigation
	 */
	protected $modelNavigation;


	public function __construct(FrontModule\Model\Airport $airport, Navigation $modelNavigation)
	{
		$this->airport = $airport;
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

		$this->template->list = $this->airport->getList();
		
		$this->render($config);
	}



}