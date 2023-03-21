<?php

namespace App\FrontModule\Components;

use Nette,
Andweb,
App\Components\VisualPaginator;

use App\FrontModule\Model\Navigation;
use App\FrontModule;
use Nette\Application\UI;


class AdSearch extends FrontControl
{
	protected $config = [
		'template' => 'default',
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
	 * @var array
	 */
	public $onSearch;


	public function __construct(FrontModule\Model\Ad $model, Navigation $modelNavigation)
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
		$this->template->stats = $this->model->getStats();

		$this->render($config);
	}


	public function createComponentForm()
	{
		$form = new Nette\Application\UI\Form;
		$form->setMethod('GET');

		$form->addText('q', 'q')
			->setRequired(true)
			->addRule($form::MIN_LENGTH, 'Piloťáku, něco sem napiš ;-)', 2);

		$form->addSubmit('submit', 'Hledat');

		$form->onSubmit[] = function ($form) {

			//$this->redirect('//this', ['q' => $form->getValues()['q']]);

			$this->onSearch($this, $form->getValues()['q']);
			// $this->page = 1;
			// $this->f = (array) $form->getValues()['f'];
			// $this->redrawControl('list');
			// $this->redrawControl('pager');

			// https://naja.js.org/#/history?id=history-mode
			// $this->presenter->payload->postGet = TRUE;
			// $this->presenter->payload->url = $this->link('this');

		};

		return $form;
	}



}