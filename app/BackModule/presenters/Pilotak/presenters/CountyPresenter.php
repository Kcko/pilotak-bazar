<?php

namespace App\BackModule\PilotakModule\Presenters;

use App\BackModule\FastAdminModule\Presenters\FastAdminTablePresenter;


class CountyPresenter extends FastAdminTablePresenter
{
	//use FastAdminLangTrait;

	public $moduleName = 'Kraje';
	protected $table = 'county';
	protected $colsOrder = array('name', 'country_id');



	public function startup()
	{
		parent::startup();

		$this->addEditTab('Obecné*8a*12', [
			['name*12'],
			['country_id*12'],
			['county_nested_id*12'],
		]);


		$this->addEditTab('Vazby*12b', [
			'county_navigation' => [
				'navigation_id',
				'image_id',
				'county_nav_user'

			]
		]);


	}

}