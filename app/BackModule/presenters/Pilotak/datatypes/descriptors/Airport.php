<?php
namespace Andweb\Datatypes\Descriptors;

use Andweb\Datatypes\Descriptors\Table,
Andweb\Datatypes\Descriptors\Tables;

use Andweb\Database\Table\Datatypes\ITableExtension;

abstract class Airport extends Table
{

	protected function init()
	{
		parent::init();
		$list = $this->addHasMany(DescriptorFactory::create(Tables\AirportAirplane::class, 'airport_airplane', 'Vyhlídkové lety'));
		$list(ITableExtension::class)->setOrder('rank ASC');

	}

}