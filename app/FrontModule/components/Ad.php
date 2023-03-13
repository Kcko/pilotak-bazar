<?php

namespace App\FrontModule\Components;

use Nette,
Andweb,
App\Components\VisualPaginator;

use App\FrontModule\Model\Navigation;
use App\FrontModule;
use Nette\Application\UI;


class Ad extends AbstractList
{
	protected $config = [
		'template' => 'default',
		'limit' => 16,
		'paging' => true,
		'filters' => false,
		'type' => 'offer',
		'year' => null,
		'filter' => null, //  null | exposed | archived
		'defaultFilter' => null,
		'homepage' => false,
		'categories' => [],
	];

	/**
	 * @var FrontModule\Model\Ad
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
	public $year;

	/**
	 * @var string
	 * @persistent
	 */
	public $filter;

	/**
	 * @var string
	 * @persistent
	 */
	public $q;

	/**
	 * @var array
	 */
	private $categories = [];


	public function __construct(FrontModule\Model\Ad $model, Navigation $modelNavigation)
	{
		$this->model = $model;
		$this->modelNavigation = $modelNavigation;
		parent::__construct();
	}

	public function getCurrentConfig(array $config = [], $lastConfig = FALSE)
	{
		$config = parent::getCurrentConfig($config);

		if ($this->year) {
			$config['year'] = $this->year;
		}

		if ($this->filter) {
			$config['filter'] = $this->filter;
		}

		if ($config['year']) {
			$config['filter'] = null;
		}

		return $config;
	}


	public function handleSetYear($year)
	{
		$this->year = $year;
		$this->filter = null;
		$this->redrawControl();
	}


	public function handleSetFilter($filter)
	{
		$this->filter = $filter;
		$this->year = null;
		$this->redrawControl();
	}


	public function setCategories(array $categories)
	{
		$this->categories = $categories;
	}

	public function handleSetQuery($q)
	{
		$this->q = $q;
		$this->redrawControl();
	}


	public function handleResetQuery()
	{
		$this->redrawControl();
	}


	public function renderDefault(array $config = [])
	{
		$config = $this->getCurrentConfig($config);

		// \Tracy\Debugger::barDump($config);
		// \Tracy\Debugger::barDump($this);
		$options = [];

		list($limit, $offset) = $this->getPages($config);
		$this->template->advertisements = $this->model->getList(
			$options,
			$limit,
			$offset
		);

		$this->template->config = $config;

		\Tracy\Debugger::barDump($this->categories);

		$this->render($config);
	}


	public function count(array $config = [])
	{
		$config = $this->getCurrentConfig($config);

		return $this->model->count(
			$config
		);
	}


	public function createComponentSearchForm()
	{
		$form = new UI\Form;
		$form->setMethod('GET');
		// tohle je tu kvuli vycisteni persistentnich parametru; jinak to snad nejde nebo nevim jak ;]
		$form->setAction($this->link('setQuery!', ['year' => null, 'filter' => null, 'p-p' => null]));
		$form->getElementPrototype()->setClass('ajax');
		$form->addText('q', 'Sem napište, co hledáte ...')
			->setRequired('Zkuste něco napsat ...')
			->addRule($form::MIN_LENGTH, 'Zadejte alespoň 3 znaky', 3);
		// $form->addHidden('year');
		// $form->addHidden('filter');
		// $form['boards-filter']->setValue(null);
		// $form['boards-year']->setValue(null);

		// $form->addHidden('boardsYear', null)
		// ->setHtmlAttribute('name', 'boards-year')
		// ->setOmitted();

		// $form->addHidden('boardsFilter', null)
		// ->setHtmlAttribute('name', 'boards-filter')
		// ->setOmitted();

		// $form->addHidden('boardsQ', null)
		// ->setHtmlAttribute('name', 'boards-q')
		// ->setOmitted();

		$form->addSubmit('_submit', 'Submit');


		if ($this->q) {
			$form['q']->setValue($this->q);
		}

		$form->onSuccess[] = function ($form, $values) {
			// $this->filter = null;
			// $this->year = null;
			// $this->getComponent('p')->p = null;
			$this->handleSetQuery($values->q);
			//$this->q = $values->q;
			//$this->filter = null;
			//$this->year = null;
			//$this->getComponent('p')->p = null;
			//$this->redrawControl();

		};

		return $form;
	}

}