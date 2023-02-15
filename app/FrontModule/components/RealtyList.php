<?php

namespace App\FrontModule\Components;

use Nette,
Andweb,
App\Components\VisualPaginator;

use App\FrontModule\Model\Navigation;
use App\FrontModule;
use Nette\Application\UI;


class RealtyList extends AbstractList
{
	protected $config = [
		'template' => 'default',
		'limit' => 6,
		'paging' => true,
		'filters' => false,
	];

	/**
	 * @var FrontModule\Model\RealtyList
	 */
	protected $model;

	/**
	 * @var Navigation
	 */
	protected $modelNavigation;

	/**
	 * @var int
	 * @persistent
	 */
	public $page = 1;

	/**
	 * @var array
	 * @persistent
	 */
	public $f = [];


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
		$count = $this->count($this->f);

		if ($this->page < 1) {
			$this->page = 1;
		}

		$offset = $this->page * $config['limit'] - $config['limit'];

		$this->template->isAjax = false;

		if ($this->presenter->isAjax()) {
			$this->template->isAjax = true;
		}

		if ($this->template->isAjax) {
			//$this->template->rows = $this->model->getList($config['limit'], $offset);
			$this->template->rows = $this->model->getList($this->f, $this->page * $config['limit'], 0);
		} else {
			$this->template->rows = $this->model->getList($this->f, $this->page * $config['limit'], 0);
		}
		$this->template->config = $config;

		$this->template->showPager = $count > $offset + $config['limit'];

		\Tracy\Debugger::barDump($count);
		\Tracy\Debugger::barDump($offset);
		\Tracy\Debugger::barDump($config['limit']);

		$this->render($config);
	}


	public function handleSetPage(int $page = 0)
	{
		$this->page = $page;


		$this->redrawControl('list');
		$this->redrawControl('pager');
	}


	public function count(array $config = [])
	{
		$config = $this->getCurrentConfig($config);

		return $this->model->count($this->f);
	}


	public function createComponentFilterForm()
	{
		$form = new Nette\Application\UI\Form();
		$form->setMethod('GET');
		$form->setTranslator($this->presenter->translator);

		$f = $form->addContainer('f');
		$f->addCheckboxList('type', 'type', [0 => 'Vše'] + $this->model->filterListAvailable('type'))->setDefaultValue(0);
		$f->addCheckboxList('location', 'location', [0 => 'Vše'] + $this->model->filterListAvailable('location'))->setDefaultValue(0);
		$f->addCheckboxList('disposition', 'disposition', [0 => 'Vše'] + $this->model->filterListAvailable('disposition'))->setDefaultValue(0);
		$f->addCheckboxList('size', 'size', [0 => 'Vše'] + $this->model->filterListAvailable('size'))->setDefaultValue(0);

		$form->addSubmit('search', 'Hledat');

		$form->onSubmit[] = function ($form) {


			$this->page = 1;
			$this->f = (array) $form->getValues()['f'];
			$this->redrawControl('list');
			$this->redrawControl('pager');


			// https://naja.js.org/#/history?id=history-mode
			$this->presenter->payload->postGet = TRUE;
			$this->presenter->payload->url = $this->link('this');

		};

		return $form;
	}
}