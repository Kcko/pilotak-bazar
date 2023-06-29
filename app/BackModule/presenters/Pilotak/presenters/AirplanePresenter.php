<?php

namespace App\BackModule\PilotakModule\Presenters;

use App\BackModule\FastAdminModule\Presenters\FastAdminTablePresenter;


class AirplanePresenter extends FastAdminTablePresenter
{
	//use FastAdminLangTrait;

	public $moduleName = 'Letadla a vrtulníky';
	protected $table = 'airplane';
	protected $colsOrder = array('image_id', 'name', 'person_no', 'active');



	public function startup()
	{
		parent::startup();

		$this->addEditTab('Obecné*8a*12', [
			['name*12'],
			['image_id*12'],
			['short_content*12'],
			['person_no*12'],
			['active*12'],
		]);

	}

}