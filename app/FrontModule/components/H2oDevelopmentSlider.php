<?php

namespace App\FrontModule\Components;

use Nette,
Andweb,
App\Components\VisualPaginator;

use App\FrontModule\Model\Navigation;
use App\FrontModule;
use Nette\Application\UI;


class H2oDevelopmentSlider extends AbstractList
{
	protected $config = [
		'template' => 'default',
		'limit' => 100,
		'paging' => false,
		'filters' => false,
	];

	/**
	 * @var FrontModule\Model\H2oDevelopment
	 */
	protected $model;

	/**
	 * @var Navigation
	 */
	protected $modelNavigation;


	public function __construct(FrontModule\Model\H2oDevelopment $model, Navigation $modelNavigation)
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
		$row = $this->model->getListOnHomepage($limit, $offset)->fetch();
		$rows = [];
		for ($i = 1; $i <= 10; $i++) {
			if ($row['pos' . $i . '__development_list_id'] && $row->{'pos' . $i . '__development_list'}) {
				$rows[] = $row->{'pos' . $i . '__development_list'};
			}
		}

		$this->template->rows = $rows;
		$this->template->config = $config;

		$this->render($config);
	}


	public function count(array $config = [])
	{
		$config = $this->getCurrentConfig($config);

		return $this->model->count();
	}
}