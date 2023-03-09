<?php

namespace App\BackModule\PilotakModule\Presenters;

use App\BackModule\FastAdminModule\Presenters\FastAdminTablePresenter;


class AdvertItemPresenter extends FastAdminTablePresenter
{
	//use FastAdminLangTrait;

	public $moduleName = 'Reklamní blok - služby';
	protected $table = 'advert_item';
	protected $colsOrder = array('heading', 'image_id', 'url', 'rank');



	public function startup()
	{
		parent::startup();

		$this->addEditTab('Obecné*8a*12', [
			['heading*12'],
			['content*12'],
			['image_id*12'],
			['url*12'],
			['rank*12'],
		]);
	}

}