<?php

namespace App\FrontModule\Presenters;

use Nette,
Andweb,
App;

use App\FrontModule\Model;


class AddAdPresenter extends FrontPresenter
{


	/**
	 * @var Model\Ad
	 * @inject
	 */
	public $model;


	public function startup()
	{
		parent::startup();
	}


	public function actionDefault($q = null, $f = [])
	{

		

		$category = $this->navigation->getById($this->presenter->navigation->navItem['id']);
		$childrenAll = $this->navigation->getAdjacencyList()->getChildrenRecursive($this->presenter->navigation->navItem['id']);
		$parents = $category->getParents();

		$this['breadcrumbs']->removeCrumb(0);
		$this['breadcrumbs']->removeCrumb(1);
		$this['breadcrumbs']->removeCrumb(2);

		foreach (array_reverse($parents) as $parent) {
			$this['breadcrumbs']->addCrumb($parent->title, $this->presenter->link('Ad:default', ['navId' => $parent->id, 'q' => null, 'f' => null]));
		}
		$this['breadcrumbs']->addCrumb($this->presenter->navigation->navItem['title']);
	}

}