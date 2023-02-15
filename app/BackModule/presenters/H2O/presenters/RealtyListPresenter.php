<?php

namespace App\BackModule\H2OModule\Presenters;

use App\BackModule\FastAdminModule\Presenters\FastAdminTablePresenter;
use App\FrontModule\Model\NavigationWorker;
use App\BackModule\FastAdminModule\Presenters\Traits\FastAdminLangTrait;

class RealtyListPresenter extends FastAdminTablePresenter
{
	use FastAdminLangTrait;
	use \App\FrontModule\Model\HtmlCleaner;

	public $moduleName = 'Realty';
	protected $table = 'realty_list';
	protected $colsOrder = array('cover__image_id', 'heading', 'rank');

	/**
	 * Navigation worker
	 *
	 * @inject
	 * @var NavigationWorker
	 */
	public $navigationWorker;


	public function startup()
	{
		parent::startup();


		$this->onAfterSave[] = [$this, 'createOrUpdateNavItem'];
		$this->onAfterDelete[] = [$this, 'removeNavItem'];

		$this->addEditTab('Obecné*6a*12', [
			['navigation_id*12'],
			['heading*12'],
			['subheading*12'],
			['cover__image_id*12'],
			['short_content*12'],
			['long_content*12'],
			['claim*12'],
			['file_id*12'],
			['rank*12'],

		]);

		// $this->addEditTab('Vazby*4c*12', [

		// 	['realty_list_param_main*12'],
		// 	['realty_list_param_rest*12'],

		// ]);


		$this->addEditTab('Ostatní*6b*12', [
			['realty_list_realty_list_type*12'],
			['realty_list_realty_list_location*12'],
			['realty_list_realty_list_disposition*12'],
			['realty_list_realty_list_size*12'],
			['realty_list_photo*12'],

		]);



		$this->addEditTab('Hlavní parametry*6b*12c', [
			'realty_list_param_main' => ['name', 'value', 'rank']
		]);

		$this->addEditTab('Ostatní parametry*6b*12c', [
			'realty_list_param_rest' => ['name', 'rank']
		]);


	}

	public function createComponentEditForm($name, $formOptions = array())
	{
		$form = parent::createComponentEditForm($name, $formOptions);

		$form['navigation_id']->setPresenterDepends([
			'Realty:default'
		]);

		$form['navigation_id']->setDefaultValue(705);

		return $form;
	}


	public function createOrUpdateNavItem($editRow, $values, $form, $oldValues)
	{
		$oldParentNavigationId = NULL;

		if ($oldValues) {
			$oldParentNavigationId = $oldValues['navigation_id'];
		}

		$parentNavigationId = $editRow['navigation_id'];

		$this->navigationWorker->createOrUpdateNavItem(
			$oldParentNavigationId,
			$parentNavigationId,
			trim(preg_replace('/\s+/', ' ', $editRow->heading)), // odstraneni new lines z nadpisu clanku
			'detail',
			$editRow->id,
		NULL, //presenter id
			'', // page description
		null
		);
	}

	public function removeNavItem($editRow)
	{
		$oldParentNavigationId = $editRow['navigation_id'];

		$this->navigationWorker->createOrUpdateNavItem(
			$oldParentNavigationId,
		NULL,
		NULL,
			'detail',
			$editRow->id
		);
	}

}