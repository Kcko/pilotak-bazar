<?php

namespace App\FrontModule\Components;

use Nette,
Andweb,
App\Components\VisualPaginator;

use App\FrontModule\Model\Navigation;
use App\FrontModule;
use Nette\Application\UI;


class AdCategoryMenu extends FrontControl
{
	protected $config = [
		'template' => 'default',
		'parent' => null,
	];

	/**
	 * @var FrontModule\Model\Ad
	 */
	protected $model;

	/**
	 * @var Navigation
	 */
	protected $modelNavigation;

	const AD_TYPES = [
		1 => 'offers',
		2 => 'demands'
	];


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

		$parentCategory = $this->modelNavigation->getById($config['parent']);
		$children = $parentCategory->getChildren();
		$childrenAll = $this->modelNavigation->getAdjacencyList()->getChildrenRecursive($config['parent']);

		// rodic -> deti
		$sortedByParent = [];
		$childrenToParent = [];
		foreach ($childrenAll as $ch) {
			$sortedByParent[$ch->parent__navigation_id][] = $ch->id;
			$childrenToParent[$ch->id] = $ch->parent__navigation_id;
		}

		$info = [];

		// pocet inzeratu v kategoriich
		$adCounts = $this->model->getAdInCategoriesCount();

		foreach ($adCounts as $adId => $arr) {

			$parent = null;
			if (isset($childrenToParent[$adId])) {
				$parent = $childrenToParent[$adId];
			}

			if (!$parent) {
				continue;
			}

			foreach (self::AD_TYPES as $typeId => $typeName) {
				if (!isset($info[$parent]['adCount'][$typeName])) {
					$info[$parent]['adCount'][$typeName] = 0;
				}

				if (isset($childrenToParent[$adId]) && isset($arr[$typeId])) {
					$info[$parent]['adCount'][$typeName] += $arr[$typeId];
				}
			}
		}


		// deti pod konkretnim rodicem, zde config[parent]
		foreach ($children as $ch) {
			if (isset($sortedByParent[$ch->id])) {
				$info[$ch->id]['subCategories'] = $sortedByParent[$ch->id];
			}
		}


		// serazeni podle navigacniho stromu
		$listByTree = $this->modelNavigation->getTable()
			->where('parent__navigation_id', $config['parent'])
			->order('rank');

		$this->template->listByTree = $listByTree;
		$this->template->info = $info;
		$this->template->adCounts = $adCounts;


		$this->render($config);
	}



}