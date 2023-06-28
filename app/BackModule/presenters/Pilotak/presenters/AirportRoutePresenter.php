<?php

namespace App\BackModule\PilotakModule\Presenters;

use App\BackModule\FastAdminModule\Presenters\FastAdminTablePresenter;


class AirportRoutePresenter extends FastAdminTablePresenter
{
	//use FastAdminLangTrait;

	public $moduleName = 'Vyhlídkové lety - trasy';
	protected $table = 'airport_route';
	protected $colsOrder = array('airport_id', 'heading', 'rank');



	public function startup()
	{
		parent::startup();

		$this->addEditTab('Obecné*8a*12', [
			['airport_id*12'],
			['heading*12'],
			['content*12'],
			['rank*12'],
		]);

	}

}