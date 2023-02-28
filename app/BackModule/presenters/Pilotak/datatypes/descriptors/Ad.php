<?php
namespace Andweb\Datatypes\Descriptors;

use Andweb\Datatypes\Descriptors\Table,
Andweb\Datatypes\Descriptors\Tables;

use Andweb\Database\Table\Datatypes\ITableExtension;

abstract class Ad extends Table
{

	protected function init()
	{

		parent::init();

		//$this->addHasMany(DescriptorFactory::create(Tables\ArticleCat::class, 'article_cat', 'Zobrazit také v kategoriích'));

		$photos = $this->addHasMany(DescriptorFactory::create(Tables\AdPhoto::class, 'ad_photo', 'Fotogalerie'));
		$photos(ITableExtension::class)->setOrder('rank ASC');


	}

}