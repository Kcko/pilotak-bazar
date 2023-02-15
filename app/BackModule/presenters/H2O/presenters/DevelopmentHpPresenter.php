<?php

namespace App\BackModule\H2OModule\Presenters;

use App\BackModule\FastAdminModule\Presenters\FastAdminTablePresenter;
use App\FrontModule\Model\NavigationWorker;
use App\BackModule\FastAdminModule\Presenters\Traits\FastAdminLangTrait;

class DevelopmentHpPresenter extends FastAdminTablePresenter
{
	//use FastAdminLangTrait;
	use \App\FrontModule\Model\HtmlCleaner;

	public $moduleName = 'Development - výběr na HP';
	protected $table = 'development_hp';
	protected $colsOrder = array('name');

	public $canAdd = false;
	public $canDelete = false;


	public function startup()
	{
		parent::startup();

		$this->addEditTab('Obecné*8a*12', [
			['name*12'],
			['pos1__development_list_id*12'],
			['pos2__development_list_id*12'],
			['pos3__development_list_id*12'],
			['pos4__development_list_id*12'],
			['pos5__development_list_id*12'],
			['pos6__development_list_id*12'],
			['pos7__development_list_id*12'],
			['pos8__development_list_id*12'],
			['pos9__development_list_id*12'],
			['pos10__development_list_id*12'],

		]);


	}

}