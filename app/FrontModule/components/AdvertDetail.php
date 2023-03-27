<?php

namespace App\FrontModule\Components;

use Nette,
Andweb,
App\Components\VisualPaginator;

use App\FrontModule\Model\Navigation;
use App\FrontModule;
use Nette\Application\UI;


class AdvertDetail extends FrontControl
{
	protected $config = [
		'template' => 'default',
		'parent' => null,
	];

	/**
	 * @var FrontModule\Model\AdvertDetail
	 */
	protected $model;

	/**
	 * @var Navigation
	 */
	protected $modelNavigation;



	public function __construct(FrontModule\Model\AdvertDetail $model, Navigation $modelNavigation)
	{
		$this->model = $model;
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
		$this->template->adverts = $this->model->listAdverts(20);

		$this->render($config);
	}



}