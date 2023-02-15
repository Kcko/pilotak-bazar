<?php

namespace App\FrontModule\Components;

use Nette,
Andweb,
App\Components\VisualPaginator;

use App\FrontModule\Model\Navigation;
use App\FrontModule;
use Nette\Application\UI;


class H2oServices extends AbstractList
{
	protected $config = [
		'template' => 'default',
		'navigation_id' => 0,
	];

	/**
	 * @var FrontModule\Model\H2oServices
	 */
	protected $model;

	/**
	 * @var Navigation
	 */
	protected $modelNavigation;


	public function __construct(FrontModule\Model\H2oServices $model, Navigation $modelNavigation)
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

		$this->template->rows = $this->model->getList($config['navigation_id']);
		$this->template->config = $config;

		$this->render($config);
	}


	public function count(array $config = [])
	{
		$config = $this->getCurrentConfig($config);

		return $this->model->count();
	}
}