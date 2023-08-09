<?php

namespace App\BackModule\PilotakModule\Presenters;

use App\BackModule\FastAdminModule\Presenters\FastAdminTablePresenter;


class FaqPresenter extends FastAdminTablePresenter
{
	//use FastAdminLangTrait;

	public $moduleName = 'FAQ';
	protected $table = 'faq';
	protected $colsOrder = array('heading');



	public function startup()
	{
		parent::startup();

		$this->addEditTab('Obecné*8a*12', [
			['heading*12'],
			['content*12'],
			['rank*12'],
		]);
	}

}