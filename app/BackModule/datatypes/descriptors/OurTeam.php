<?php
namespace Andweb\Datatypes\Descriptors;

use Andweb\Datatypes\Descriptors\Table,
  Andweb\Datatypes\Descriptors\Tables;

abstract class OurTeam extends HasLangTable 
{

	protected function init() 
	{    

		parent::init();

		$this->addHasMany(DescriptorFactory::create(Tables\OurTeamCountry::class, 'our_team_country', 'Jazyky'));



	}

}