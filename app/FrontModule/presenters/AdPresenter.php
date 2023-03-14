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
		// basic ad info
		$ad = $this->template->ad = $this->model->getById($navParam);

		if (!$this->template->ad) {
			$this->error();
		}


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
			$this['breadcrumbs']->addCrumb($parent->title, $parent->getUrl());
		}
		$this['breadcrumbs']->addCrumb($ad->heading);


		// back to parent
		$this->template->backUrl = $category->parent__navigation->getUrl();


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