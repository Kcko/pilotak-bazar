<?php

namespace App\BackModule\H2OModule\Presenters;

use App\BackModule\FastAdminModule\Presenters\FastAdminTablePresenter;
use App\FrontModule\Model\NavigationWorker;
use App\BackModule\FastAdminModule\Presenters\Traits\FastAdminLangTrait;

class ReferenceListPresenter extends FastAdminTablePresenter
{
	use FastAdminLangTrait;
	use \App\FrontModule\Model\HtmlCleaner;

	public $moduleName = 'Reference';
	protected $table = 'reference_list';
	protected $colsOrder = array('cover__image_id', 'heading', 'rank');


	public function startup()
	{
		parent::startup();

		$this->addEditTab('Obecné*8a*12', [
			['heading*12'],
			['cover__image_id*12'],
			['content*12'],
			['category*12'],
			['customer*12'],
			['subject*12'],
			['period*12'],
			['team*12'],
			['rank*12'],

		]);

	}

}