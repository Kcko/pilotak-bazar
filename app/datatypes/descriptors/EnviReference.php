<?php
namespace Andweb\Datatypes\Descriptors;

use Andweb\Datatypes\Descriptors\Table,
Andweb\Datatypes\Descriptors\Tables;

abstract class EnviReference extends HasLangTable
{
	protected function init() 
	{
		parent::init();
		
		$this->addHasMany(DescriptorFactory::create(Tables\EnviReferencePhoto::class, 'photos', 'Fotogalerie'));
		$this->addHasMany(DescriptorFactory::create(Tables\EnviReferenceNavigation::class, 'cats', 'Kategorie'));
	}
}