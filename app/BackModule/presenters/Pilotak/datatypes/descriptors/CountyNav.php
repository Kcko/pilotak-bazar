<?php
namespace Andweb\Datatypes\Descriptors;

use Andweb\Datatypes\Descriptors\Table,
Andweb\Datatypes\Descriptors\Tables;

use Andweb\Database\Table\Datatypes\ITableExtension;

abstract class CountyNav extends Table
{

	protected function init()
	{

		parent::init();

		//$this->addHasMany(DescriptorFactory::create(Tables\ArticleCat::class, 'article_cat', 'Zobrazit také v kategoriích'));

		//$this->addHasMany(DescriptorFactory::create(Tables\CountyNav::class, 'county_navigation', 'Vazby'));
		$this->addHasMany(DescriptorFactory::create(Tables\CountyNavUser::class, 'county_nav_user', 'Uzivatele'));


	}

}