<?php

namespace App\BackModule\PilotakModule\Presenters;

use App\BackModule\FastAdminModule\Presenters\FastAdminTablePresenter;


class OrderPresenter extends FastAdminTablePresenter
{
	//use FastAdminLangTrait;

	public $moduleName = 'Objednávky';
	protected $table = 'pilotak_order';
	protected $colsOrder = array('fullname', 'created', 'airport_airplane_id', 'copilot', 'total_price');



	public function startup()
	{
		parent::startup();

		$this->addEditTab('Obecné*8a*12', [
			['fullname*12'],
			['email*12'],
			['phone*12'],
			['street*12'],
			['city*12'],
			['zip*12'],
			['gift_name*12'],
			['message*12'],
		]);

		$this->addEditTab('Ceny*4b*12', [
			['price*12'],
			['price_payment*12'],
			['total_price*12'],
		]);

		$this->addEditTab('Ostatní*4b*12', [
			['created*12'],
			['ip*12'],
		]);



		$this->addEditTab('Vyhlídkový let*4b*12', [
			['airport_id*12'],
			['airplane_id*12'],
			['airport_airplane_id*12'],
			['copilot*12'],
		]);

	


	}

}