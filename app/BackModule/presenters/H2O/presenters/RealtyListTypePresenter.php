<?php

namespace App\BackModule\H2OModule\Presenters;

use App\BackModule\FastAdminModule\Presenters\FastAdminTablePresenter;
use App\FrontModule\Model\NavigationWorker;
use App\BackModule\FastAdminModule\Presenters\Traits\FastAdminLangTrait;

class RealtyListTypePresenter extends FastAdminTablePresenter
{
    use FastAdminLangTrait;
	use \App\FrontModule\Model\HtmlCleaner;

	public $moduleName = 'Číselník realty - typ nemovitosti';
	protected $table = 'realty_list_type';
	protected $colsOrder = array('name');


	public function startup() 
    {
        parent::startup();

		$this->addEditTab('Obecné*8a*12', [
			['name*12']

		]);

	}

}