<?php

namespace App\BackModule\H2OModule\Presenters;

use App\BackModule\FastAdminModule\Presenters\FastAdminTablePresenter;
use App\FrontModule\Model\NavigationWorker;
use App\BackModule\FastAdminModule\Presenters\Traits\FastAdminLangTrait;

class ReferenceHpPresenter extends FastAdminTablePresenter
{
	//use FastAdminLangTrait;
	use \App\FrontModule\Model\HtmlCleaner;

	public $moduleName = 'Reference - výběr na HP';
	protected $table = 'reference_hp';
	protected $colsOrder = array('name');

	public $canAdd = false;
	public $canDelete = false;


	public function startup()
	{
		parent::startup();

		$this->addEditTab('Obecné*8a*12', [
			['name*12'],
			['pos1__reference_list_id*12'],
			['pos2__reference_list_id*12'],
			['pos3__reference_list_id*12'],
			['pos4__reference_list_id*12'],
			['pos5__reference_list_id*12'],
			['pos6__reference_list_id*12'],
			['pos7__reference_list_id*12'],
			['pos8__reference_list_id*12'],
			['pos9__reference_list_id*12'],
			['pos10__reference_list_id*12'],

		]);


	}

}