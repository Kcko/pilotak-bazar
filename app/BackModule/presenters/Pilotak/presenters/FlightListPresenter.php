<?php

namespace App\BackModule\PilotakModule\Presenters;

use App\BackModule\FastAdminModule\Presenters\FastAdminTablePresenter;


class FlightListPresenter extends FastAdminTablePresenter
{
	//use FastAdminLangTrait;

	public $moduleName = 'Vyhlídkové lety - slider (seznam)';
	protected $table = 'flight_list';
	protected $colsOrder = array('heading', 'subheading', 'flight_list_airport_airplane', 'active', 'rank');



	public function startup()
	{
		parent::startup();

		$this->addEditTab('Obecné*8a*12', [
			['heading*12'],
			['subheading*12'],
			['flight_list_airport_airplane*12'],
			['active*12'],
			['rank*12'],
			['config*12'],
		]);

	}

}