<?php

namespace App\BackModule\H2OModule\Presenters;

use App\BackModule\FastAdminModule\Presenters\FastAdminTablePresenter;
use App\FrontModule\Model\NavigationWorker;
use App\BackModule\FastAdminModule\Presenters\Traits\FastAdminLangTrait;

class ServiceListPresenter extends FastAdminTablePresenter
{
	use FastAdminLangTrait;
	use \App\FrontModule\Model\HtmlCleaner;

	public $moduleName = 'Služby';
	protected $table = 'service_list';
	protected $colsOrder = array('navigation_id', 'heading', 'rank');


	public function startup()
	{
		parent::startup();

		$this->addEditTab('Obecné*8a*12', [
			['navigation_id*12'],
			['heading*12'],
			['content*12'],
			['rank*12'],

		]);

	}


	public function createComponentEditForm($name, $formOptions = array())
	{
		$form = parent::createComponentEditForm($name, $formOptions);

		$form['navigation_id']->setPresenterDepends([
			'H2oServiceDetail:default'
		]);



		return $form;
	}

}