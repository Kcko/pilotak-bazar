<?php
namespace Andweb\Datatypes\Descriptors;

use Andweb\Datatypes\Descriptors\Table,
Andweb\Datatypes\Descriptors\Tables;

use Andweb\Database\Table\Datatypes\ITableExtension;

abstract class FlightList extends Table
{

	protected function init()
	{
		parent::init();
		$this->addHasMany(DescriptorFactory::create(Tables\FlightListAirportAirplane::class, 'flight_list_airport_airplane', 'Vyhlídkové lety'));

	}

}