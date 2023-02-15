<?php

namespace App\BackModule\H2OModule\Presenters;

use App\BackModule\FastAdminModule\Presenters\FastAdminTablePresenter;
use App\FrontModule\Model\NavigationWorker;
use App\BackModule\FastAdminModule\Presenters\Traits\FastAdminLangTrait;

class RealtyListLocationPresenter extends FastAdminTablePresenter
{
    use FastAdminLangTrait;
	use \App\FrontModule\Model\HtmlCleaner;

	public $moduleName = 'Číselník realty - lokalita';
	protected $table = 'realty_list_location';
	protected $colsOrder = array('name');


	public function startup() 
    {
        parent::startup();

		$this->addEditTab('Obecné*8a*12', [
			['name*12']

		]);

	}

}