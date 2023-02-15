<?php

namespace App\BackModule\H2OModule\Presenters;

use App\BackModule\FastAdminModule\Presenters\FastAdminTablePresenter;
use App\FrontModule\Model\NavigationWorker;
use App\BackModule\FastAdminModule\Presenters\Traits\FastAdminLangTrait;

class DevelopmentListPresenter extends FastAdminTablePresenter
{
	use FastAdminLangTrait;
	use \App\FrontModule\Model\HtmlCleaner;

	public $moduleName = 'Development';
	protected $table = 'development_list';
	protected $colsOrder = array('cover__image_id', 'heading', 'rank');


	public function startup()
	{
		parent::startup();

		$this->addEditTab('Obecné*8a*12', [
			['heading*12'],
			['cover__image_id*12'],
			['content*12'],
			['rank*12'],

		]);
		$this->addEditTab('Ostatní*4b*12', [
			['web*12'],
			['technical_specification*12'],
			['price*12'],
			['schedule*12'],
		]);

		$this->addEditTab('Slider*4b*12', [
			['heading_slider*12'],
			['slider_x__image_id*12'],
			['slider_y__image_id*12'],
		]);

	}

}