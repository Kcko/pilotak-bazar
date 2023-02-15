<?php

namespace App\FrontModule\Components;

use Nette,
Andweb,
App\Components\VisualPaginator;

use App\FrontModule\Model\Navigation;
use App\FrontModule;
use Nette\Application\UI;


class H2oReference extends AbstractList
{
	protected $config = [
		'template' => 'default',
		'limit' => 6,
		'paging' => true,
	];

	/**
	 * @var int
	 * @persistent
	 */
	public $page = 1;

	/**
	 * @var FrontModule\Model\H2oReference
	 */
	protected $model;

	/**
	 * @var Navigation
	 */
	protected $modelNavigation;


	public function __construct(FrontModule\Model\H2oReference $model, Navigation $modelNavigation)
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
		$count = $this->count();

		if ($this->page < 1) {
			$this->page = 1;
		}

		$offset = $this->page * $config['limit'] - $config['limit'];

		$this->template->isAjax = false;

		if ($this->presenter->isAjax()) {
			$this->template->isAjax = true;
		}

		$this->template->rows = $this->model->getList($this->page * $config['limit'], 0);
		$this->template->config = $config;
		$this->template->showPager = $count > $offset + $config['limit'];


		\Tracy\Debugger::barDump($count);
		\Tracy\Debugger::barDump($offset);
		\Tracy\Debugger::barDump($config['limit']);

		$this->render($config);
	}


	public function count(array $config = [])
	{
		$config = $this->getCurrentConfig($config);

		return $this->model->count();
	}

	public function handleSetPage(int $page = 0)
	{
		$this->page = $page;


		$this->redrawControl('list');
		$this->redrawControl('pager');
	}
}