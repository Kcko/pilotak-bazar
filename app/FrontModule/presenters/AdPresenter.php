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
	 * @var array
	 * @persistent
	 */
	public $f = [];


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


	public function actionDefault($q = null, $f = [])
	{

		/* Formular se odeslal, zpracovat hodnoty  */

		$this['adListByCategories']->onFormFiltered[] = function ($control, $form, $values) {
			$this->adType = $values->adType;

			$f = [
				'order' => $values->order,
				'adType' => $values->adType,
			];
			

			if (isset($values->priceFrom) && $values->priceFrom) {
				$f['priceFrom'] = $values->priceFrom;
			}

			if (isset($values->priceTo) && $values->priceTo) {
				$f['priceTo'] = $values->priceTo;
			}

			if (isset($values->priceWoLimit) && $values->priceWoLimit) {
				$f['priceWoLimit'] = $values->priceWoLimit;
				$f['priceFrom'] = null;
				$f['priceTo'] = null;
			} else {
				$f['priceWoLimit'] = false;
			}

			if (isset($values->county)) {
				$f['county'] = $values->county;
			}

			$this->redirect('default', ['f' => $f, 'p-p' => null]);
		};


		$this['adListByCategories']->setType($this->adType == 'offers' ? 1 : 2);

		if ($f && count($f)) {
			$this['adListByCategories']['filterForm']->setDefaults($f);
			$this['adListByCategories']['filterModalForm']->setDefaults($f);
			$this['adListByCategories']->setFilters($f);
		}


		if ($q) {
			$this['adSearch']['form']['q']->setDefaultValue($q);
			$this['adListByCategories']->setSearch($q);
			$this->template->q = $q;
		}

		/* //Formular se odeslal, zpracovat hodnoty  */


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
			$this['breadcrumbs']->addCrumb($parent->title, $this->presenter->link('Ad:default', ['navId' => $parent->id, 'q' => null]));
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


	protected function createComponentLikeControlInDetail()
	{
		$isLiked = $this->user->getId() ? $this->model->isLiked($this->template->ad->id, $this->user->getId()) : [];
		return new App\FrontModule\Components\Like($this->template->ad->id, $isLiked ? true : false, $this->model, $this->user);
	}


}