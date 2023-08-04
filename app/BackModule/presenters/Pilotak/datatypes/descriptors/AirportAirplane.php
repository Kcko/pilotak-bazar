<?php
namespace Andweb\Datatypes\Descriptors;

use Andweb\Datatypes\Descriptors\Table,
Andweb\Datatypes\Descriptors\Tables;

use Andweb\Database\Table\Datatypes\ITableExtension;

abstract class AirportAirplane extends Table
{

	protected function init()
	{
		parent::init();
		$this['tags']->setItems([
				'Action' => 'Action', 
				'Popular' => 'Popular',
				'New' => 'New',
				'Discount' => 'Discount',
				'Last Chance' => 'Last Chance',
				'Pilot exam' => 'Pilot exam',
			
			]);
		
	}

}