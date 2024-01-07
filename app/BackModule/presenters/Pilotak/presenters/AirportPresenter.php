<?php

namespace App\BackModule\PilotakModule\Presenters;

use App\BackModule\FastAdminModule\Presenters\FastAdminTablePresenter;
use App\FrontModule\Model\NavigationWorker;
use \DateTime;


class AirportPresenter extends FastAdminTablePresenter
{
	//use FastAdminLangTrait;

	public $moduleName = 'Letiště';
	protected $table = 'airport';
	protected $colsOrder = array('name', 'heading_h1', 'active');

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
		$this->onAfterSave[] = [$this, 'fixTags'];
		$this->onAfterDelete[] = [$this, 'removeNavItem'];



		$this->addEditTab('Obecné*8a*12', [
			['name*12'],
			['navigation_id*12'],
			['heading_h1*12'],
			['heading_h2*12'],
			['code*12'],
			['image_id*12'],
			['map_iframe*12'],
			['basic_info*12'],
			['map_x*6', 'map_y*6'],
			['active*12'],
		]);


		$this->addEditTab('Vyhlídkové lety*12b*12', [
			'airport_airplane' => [
				//'airport_id', 
				'airplane_id', 
				'duration', 
				'price', 
				'price_copilot', 
				'tags', 
				'active', 
				'rank', 

			]
		]);


	}


	public function createComponentEditForm($name, $formOptions = array())
	{
		$form = parent::createComponentEditForm($name, $formOptions);

		$form['navigation_id']->setPresenterDepends([
			'Airport:default'
		]);

		return $form;
	}


	public function createOrUpdateNavItem($editRow, $values, $form, $oldValues)
	{
		$oldParentNavigationId = NULL;

		if ($oldValues) {
			$oldParentNavigationId = 845; /*$oldValues['navigation_id'];*/
		}

		$parentNavigationId = 845; /*$editRow['navigation_id'];*/

		$this->navigationWorker->createOrUpdateNavItem(
			$oldParentNavigationId,
			$parentNavigationId,
			trim(preg_replace('/\s+/', ' ', $editRow->name)), // odstraneni new lines z nadpisu clanku
			'detail',
			$editRow->id,
			NULL, //presenter id
			null, /*$editRow->basic_info, */// page description
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


	public function fixTags($editRow, $values, $form, $oldValues)
	{
		$aa = $editRow->related('airport_airplane');
		foreach ($aa as $row) {
			if ($row->tags == '') {
				$row->update(['tags' => null]);
			}
		}
		
	}


}