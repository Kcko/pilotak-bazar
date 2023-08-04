<?php

namespace App\BackModule\PilotakModule\Presenters;

use App\BackModule\FastAdminModule\Presenters\FastAdminTablePresenter;


class FlightListPricingPresenter extends FastAdminTablePresenter
{
	//use FastAdminLangTrait;

	public $moduleName = 'Vyhlídkové lety - ceny';
	protected $table = 'airport_airplane';
	protected $colsOrder = array('airport_id', 'airplane_id', 'duration', 'price', 'price_copilot', 'active', 'rank');

	//public $defaultOrder = ['airplane_id' => 'ASC'];


	public function startup()
	{
		parent::startup();

		$this->addEditTab('Obecné*8a*12', [
			['airport_id*12'],
			['airplane_id*12'],
			['duration*12'],
			['price*12'],
			['price_copilot*12'],
			['active*12'],
			['tags*12'],
			['rank*12'],
		]);

	}

}