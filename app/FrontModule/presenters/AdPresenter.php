<?php

namespace App\FrontModule\Presenters;

use Nette,
Andweb,
App;

use App\FrontModule\Model;


class AdPresenter extends FrontPresenter
{

	/**
	 * @var string
	 * @persistent
	 */
	public $adType = 'offers';

	/**
	 * @var string
	 * @persistent
	 */
	public $q = '';


	/**
	 * @var Model\Ad
	 * @inject
	 */
	public $model;


	public function startup()
	{
		parent::startup();

		if (!($this->adType == 'offers' || $this->adType == 'demands')) {
			$this->error();
		}
	}


	public function actionDefault($q = null)
	{
		if ($q) {
			$this['adSearch']['form']['q']->setDefaultValue($q);
			$this['adListByCategories']->setSearch($q);
			$this->template->q = $q;
		}

		$category = $this->navigation->getById($this->presenter->navigation->navItem['id']);
		$childrenAll = $this->navigation->getAdjacencyList()->getChildrenRecursive($this->presenter->navigation->navItem['id']);
		$parents = $category->getParents();

		$this['breadcrumbs']->removeCrumb(0);
		$this['breadcrumbs']->removeCrumb(1);
		$this['breadcrumbs']->removeCrumb(2);
		foreach (array_reverse($parents) as $parent) {
			$this['breadcrumbs']->addCrumb($parent->title, $this->presenter->link('Ad:default', ['navId' => $parent->id]));
		}
		$this['breadcrumbs']->addCrumb($this->presenter->navigation->navItem['title']);

		$this->template->categories = array_merge(array_keys($childrenAll), [$category->id]);

		$this['adListByCategories']->setCategories($this->template->categories);

	}


	public function actionDetail($navParam)
	{
		// basic ad info
		$ad = $this->template->ad = $this->model->getById($navParam);

		if (!$this->template->ad) {
			$this->error();
		}

		// persitent re-set
		$this->adType = $ad->ad_type_id == 2 ? 'demands' : 'offers';

		// save last HIT 
		$session = $this->getSession('adDetail');

		if (!isset($session->lastHit)) {
			$session->lastHit = [];
		}

		if (!isset($session->lastHist[$ad->id])) {
			// hit
			$ad->update([
				'views_cnt' => $ad->views_cnt + 1,
				'last_view_date' => new \DateTime
			]);
			$session->lastHist[$ad->id] = true;
		}


		// parents, childrens, category
		$category = $this->navigation->getById($ad->navigation_id);
		$children = $category->getChildren();
		$parents = $category->getParents();
		$childrenAll = $this->navigation->getAdjacencyList()->getChildrenRecursive($ad->navigation_id);

		// \Tracy\Debugger::barDump($category);
		// \Tracy\Debugger::barDump($children);
		// \Tracy\Debugger::barDump($parents);
		// \Tracy\Debugger::barDump($childrenAll);


		// reconstructed breakdcumbs
		$this['breadcrumbs']->removeCrumb(0);
		$this['breadcrumbs']->removeCrumb(1);
		foreach (array_reverse($parents) as $parent) {
			$this['breadcrumbs']->addCrumb($parent->title, $this->presenter->link('Ad:default', ['navId' => $parent->id]));
		}
		$this['breadcrumbs']->addCrumb($ad->heading);


		// back to parent
		// $this->template->backUrl = $category->parent__navigation->getUrl();
		$this->template->category = $category;


		// photos
		$photos = [];
		if ($ad->image_id) {
			$photos[] = $ad->image->getImageUrl('hero');
		}
		foreach ($this->template->ad['ad_photo'] as $photo) {
			$photos[] = $photo->getImageUrl('hero');
		}

		if (!count($photos)) {
			$photos[] = '/assets/gfx/no-image.jpg';
		}

		$this->template->photos = $photos;

	}

}