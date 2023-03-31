<?php

namespace App\BackModule\PilotakModule\Presenters;

use App\BackModule\FastAdminModule\Presenters\FastAdminTablePresenter;
use App\FrontModule\Model\NavigationWorker;


class AdPresenter extends FastAdminTablePresenter
{
	//use FastAdminLangTrait;



	public $moduleName = 'Inzeráty';
	protected $table = 'ad';
	protected $colsOrder = array('heading', 'navigation_id', 'ad_type_id', 'created', 'expiration', 'price', 'top');


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
		$this->onAfterSave[] = [$this, 'afterSaveAd'];
		$this->onAfterDelete[] = [$this, 'removeNavItem'];


		$this->addEditTab('Obecné*6a*12a', [
			['ad_type_id*12'],
			['navigation_id*12'],
			['heading*12'],
			['content*12'],
			['created*3', 'updated*3', 'updated_cnt*3'],
			['top*3', 'top_date*3'],
			['is_visible*12'],
		]);


		$this->addEditTab('Další info*6a*12b', [
			['last_view_date*6', 'views_cnt*6'],
		]);

		$this->addEditTab('Expirace*6b*12a', [
			['expiration*12'],
			['expiration_cnt*12'],
			['expiration_renew*12'],
		]);

		$this->addEditTab('Cena*6b*12b', [
			['price*6', 'currency_id*6'],
		]);

		$this->addEditTab('Média*6b*12c', [
			['ad_photo*12'],
			['video*12'],
		]);


		$this->addEditTab('Kontakt*6b*12d', [
			['user_id*12'],
			['contact_email*6', 'contact_phone*6'],
			['contact_town*6', 'county_id*6'],
		]);
	}



	public function createComponentEditForm($name, $formOptions = array())
	{
		$form = parent::createComponentEditForm($name, $formOptions);

		$form['navigation_id']->setPresenterDepends([
			'Ad:default'
		]);

		return $form;
	}


	public function createOrUpdateNavItem($editRow, $values, $form, $oldValues)
	{
		$oldParentNavigationId = NULL;

		if ($oldValues) {
			$oldParentNavigationId = 735; /*$oldValues['navigation_id'];*/
		}

		$parentNavigationId = 735; /*$editRow['navigation_id'];*/

		$this->navigationWorker->createOrUpdateNavItem(
			$oldParentNavigationId,
			$parentNavigationId,
			trim(preg_replace('/\s+/', ' ', $editRow->heading)), // odstraneni new lines z nadpisu clanku
			'detail',
			$editRow->id,
			NULL, //presenter id
			$editRow->content, // page description
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


	public function afterSaveAd($editRow, $values, $form, $oldValues)
	{
		if ($editRow->expiration != $oldValues['expiration']) {
			$editRow->update(['expiration_cnt' => (int) $editRow->expiration_cnt + 1]);
		}

		if ($editRow->expiration_renew != '') {
			$dt = new \DateTime($editRow->expiration);
			$dt->modify("+ {$editRow->expiration_renew}day");
			$editRow->update([
				'expiration_cnt' => (int) $editRow->expiration_cnt + 1,
				'expiration' => $dt,
				'expiration_renew' => null,
			]);
		}
		//throw new \Andweb\FlashMessageException('Test');
		// \Tracy\Debugger::barDump($editRow);
		// \Tracy\Debugger::barDump($values);
		// \Tracy\Debugger::barDump($form);
		// \Tracy\Debugger::barDump($oldValues);
	}

}