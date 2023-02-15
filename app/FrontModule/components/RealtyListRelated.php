<?php

namespace App\FrontModule\Components;

use Nette,
Andweb,
App\Components\VisualPaginator;

use App\FrontModule\Model\Navigation;
use App\FrontModule;
use Nette\Application\UI;


class RealtyListRelated extends AbstractList
{
	protected $config = [
		'template' => 'default',
		'limit' => 3,
		'paging' => false,
		'filters' => false,
		'relatedId' => 0,
	];

	/**
	 * @var FrontModule\Model\RealtyList
	 */
	protected $model;

	/**
	 * @var Navigation
	 */
	protected $modelNavigation;


	public function __construct(FrontModule\Model\RealtyList $model, Navigation $modelNavigation)
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

		list($limit, $offset) = $this->getPages($config);
		$this->template->rows = $this->model->getListRelated($config['relatedId'], $limit, $offset);
		$this->template->config = $config;

		$this->render($config);
	}


	public function count(array $config = [])
	{
		$config = $this->getCurrentConfig($config);

		return $this->model->countRelated($config['relatedId']);
	}
}