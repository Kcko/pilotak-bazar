<?php

namespace App\BackModule\PilotakModule\Presenters;

use App\BackModule\FastAdminModule\Presenters\FastAdminTablePresenter;


class FlightPresenter extends FastAdminTablePresenter
{
	//use FastAdminLangTrait;

	public $moduleName = 'Vyhlídkové lety';
	protected $table = 'flight_content';
	protected $colsOrder = array('image_id', 'name', 'rank');



	public function startup()
	{
		parent::startup();

		$this->addEditTab('Obecné*8a*12', [
			['name*12'],
			['short_content*12'],
			['full_content*12'],
			['image_id*12'],
			['svg__image_id*12'],
			['button_text*6', 'button_url*6'],
			['rank*12'],
		]);

	}

}