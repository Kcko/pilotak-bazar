<?php

namespace App\FrontModule\Presenters;

use Nette,
Andweb,
App;

use App\FrontModule\Model;


class AdPresenter extends FrontPresenter
{

	/**
	 * @var Model\Ad
	 * @inject
	 */
	public $model;


	public function actionDefault()
	{

		$category = $this->navigation->getById($this->presenter->navigation->navItem['id']);
		$childrenAll = $this->navigation->getAdjacencyList()->getChildrenRecursive($this->presenter->navigation->navItem['id']);

		$this->template->categories = array_merge(array_keys($childrenAll), [$category->id]);

		$this['adListByCategories']->setCategories($this->template->categories);



	}

	public function actionDetail($navParam)
	{

		$ad = $this->template->ad = $this->model->getById($navParam);

		if (!$this->template->ad) {
			$this->error();
		}

		$category = $this->navigation->getById($ad->navigation_id);
		$children = $category->getChildren();
		$parents = $category->getParents();
		$childrenAll = $this->navigation->getAdjacencyList()->getChildrenRecursive($ad->navigation_id);

		// \Tracy\Debugger::barDump($category);
		// \Tracy\Debugger::barDump($children);
		// \Tracy\Debugger::barDump($parents);
		// \Tracy\Debugger::barDump($childrenAll);

		$this['breadcrumbs']->removeCrumb(0);
		$this['breadcrumbs']->removeCrumb(1);
		foreach (array_reverse($parents) as $parent) {
			$this['breadcrumbs']->addCrumb($parent->title, $parent->getUrl());
		}
		$this['breadcrumbs']->addCrumb($ad->heading);


		$this->template->backUrl = $category->parent__navigation->getUrl();


	}
}