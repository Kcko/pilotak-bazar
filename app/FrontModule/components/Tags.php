<?php

namespace App\FrontModule\Components;

use Nette,
Andweb,
App\Components\VisualPaginator;

use App\FrontModule\Model\Navigation;
use App\FrontModule;
use Nette\Application\UI;


class Tags extends FrontControl
{	


	protected $config = [
		'template' => 'default',
		'tags' => [],
	];

	// 'Action','Popular','New','Discount','Last Chance'


	public function __construct()
	{
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

		$this->template->tags = $this->formatTags($config['tags']);
		
		$this->render($config);
	}


	public function formatTags($tags)
	{
		$tags = explode(',', $tags);
		return array_filter($tags);
	}

}