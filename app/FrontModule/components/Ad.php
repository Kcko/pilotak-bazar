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
		'type' => null,
		'year' => null,
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
	 * @var int
	 */
	public $type;

	/**
	 * @var array
	 */
	private $categories = [];

	public $onFormFiltered = [];

	/**
	 * @var int
	 */
	public $itemsTotal;

	/**
	 * @var Nette\Security\User
	 */
	protected $user;


	public function __construct(FrontModule\Model\Ad $model, Navigation $modelNavigation, Nette\Security\User $user)
	{
		$this->model = $model;
		$this->modelNavigation = $modelNavigation;
		$this->user = $user;
		parent::__construct();
	}

	public function getCurrentConfig(array $config = [], $lastConfig = FALSE)
	{
		$config = parent::getCurrentConfig($config);

		if ($this->categories) {
			$config['categories'] = $this->categories;
		}

		if ($this->q) {
			$config['q'] = $this->q;
		}

		if ($this->type) {
			$config['type'] = $this->type;
		}

		return $config;
	}


	public function handleSetFilter($filter)
	{
		$this->filter = $filter;
		$this->redrawControl();
	}


	public function setCategories(array $categories)
	{
		$this->categories = $categories;
	}

	public function setType(int $type)
	{
		$this->type = $type;
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

		list($limit, $offset) = $this->getPages($config);

		$this->template->advertisements = $this->model->getList(
			$config,
			$limit,
			$offset
		);

		$this->template->config = $config;

		$this->render($config);

	}

	public function renderSearchTitle(array $config = [])
	{

		$config = $this->getCurrentConfig($config);


		$this->template->config = $config;

		$this->template->q = $this->q;
		$this->template->searchResultCnt = $this->count($config);

		$this->render($config);

	}


	public function count(array $config = [])
	{
		static $cache;

		if ($cache === null) {
			$config = $this->getCurrentConfig($config);
			$cache = $this->model->count($config);
		}

		return $cache;
	}


	public function createComponentFilterForm()
	{
		$form = new UI\Form;
		$form->setMethod('GET');
		// tohle je tu kvuli vycisteni persistentnich parametru; jinak to snad nejde nebo nevim jak ;]
		//$form->setAction($this->link('setQuery!', ['year' => null, 'filter' => null, 'p-p' => null]));
		//$form->getElementPrototype()->setClass('ajax');
		$form->addRadioList('adType', 'Typ inzerátu', [
			'offers' => 'Nabídky',
			'demands' => 'Poptávky'
		])->setDefaultValue('offers');

		$form->addSelect('order', 'Seřadit dle:', [
			'newest' => 'Nejnovější',
			'priceAsc' => 'Nejlevnější',
			'priceDesc' => 'Nejdražší',
		])->setDefaultValue('newest');


		$form->addSubmit('submit', 'Submit');


		$form->onSuccess[] = function ($form, $values) {

			$this->onFormFiltered($this, $form, $values);
		};

		return $form;
	}



	public function createComponentFilterModalForm()
	{
		$form = new UI\Form;
		$form->setMethod('GET');
		// tohle je tu kvuli vycisteni persistentnich parametru; jinak to snad nejde nebo nevim jak ;]
		//$form->setAction($this->link('setQuery!', ['year' => null, 'filter' => null, 'p-p' => null]));
		//$form->getElementPrototype()->setClass('ajax');
		$form->addRadioList('adType', 'Typ inzerátu', [
			'offers' => 'Nabídky',
			'demands' => 'Poptávky'
		])->setDefaultValue('offers');

		$form->addSelect('order', 'Seřadit dle:', [
			'newest' => 'Nejnovější',
			'priceAsc' => 'Nejlevnější',
			'priceDesc' => 'Nejdražší',
		])->setDefaultValue('newest');

		$form->addSelect('county', 'Kraj:', $this->model->listCounty())->setPrompt('Všude');

		$form->addCheckbox('price_wo_limit', 'Cena bez omezení:')->setDefaultValue(1);
		$form->addText('price_from', 'Cena od:');
		$form->addText('price_to', 'Cena do:');


		$form->addSubmit('submit', 'Submit');


		$form->onSuccess[] = function ($form, $values) {

			$this->onFormFiltered($this, $form, $values);
		};

		return $form;
	}


	public function setSearch($q)
	{
		$this->q = $q;
	}


	protected function createComponentLikeControl()
	{
		$allLikes = $this->user->getId() ? $this->model->allLikesByUser($this->user->getId()) : [];

		return new Nette\Application\UI\Multiplier(function (int $adId) use ($allLikes) {
			return new Like($adId, $allLikes[$adId] ?? false, $this->model, $this->user);
		});
	}

}