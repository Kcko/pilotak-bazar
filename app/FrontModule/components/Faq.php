<?php

namespace App\FrontModule\Components;

use Nette,
Andweb,
App\Components\VisualPaginator;

use App\FrontModule\Model\Navigation;
use App\FrontModule;
use Nette\Application\UI;


class Faq extends FrontControl
{
	protected $config = [
		'template' => 'default',
	];

	/**
	 * @var FrontModule\Model\Faq
	 */
	protected $model;

	/**
	 * @var Navigation
	 */
	protected $modelNavigation;



	public function __construct(FrontModule\Model\Faq $model, Navigation $modelNavigation)
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
		$this->template->faq = $this->model->getList();

		$this->render($config);
	}



}