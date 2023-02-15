<?php
namespace Andweb\Datatypes\Descriptors;

use Andweb\Datatypes\Descriptors\Table,
Andweb\Datatypes\Descriptors\Tables;

abstract class EnviDownload extends HasLangTable
{
	protected function init() 
	{
		parent::init();
		$this->addHasMany(DescriptorFactory::create(Tables\EnviDownloadNavigation::class, 'cats', 'Kategorie'));
	}
}