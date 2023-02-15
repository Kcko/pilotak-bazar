<?php
namespace Andweb\Datatypes\Descriptors;

use Andweb\Datatypes\Descriptors\Table,
  Andweb\Datatypes\Descriptors\Tables;

use Andweb\Database\Table\Datatypes\ITableExtension;

abstract class RealtyList extends HasLangTable 
{

	protected function init() 
	{    

		parent::init();

		//$this->addHasMany(DescriptorFactory::create(Tables\ArticleCat::class, 'article_cat', 'Zobrazit také v kategoriích'));

		$photos = $this->addHasMany(DescriptorFactory::create(Tables\RealtyListPhoto::class, 'realty_list_photo', 'Fotogalerie'));
		$photos(ITableExtension::class)->setOrder('rank ASC');

		$params = $this->addHasMany(DescriptorFactory::create(Tables\RealtyListParamMain::class, 'realty_list_param_main', 'Hlavní parametry'));
		$params(ITableExtension::class)->setOrder('rank ASC');

		$params = $this->addHasMany(DescriptorFactory::create(Tables\RealtyListParamRest::class, 'realty_list_param_rest', 'Vedlejší parametry'));
		$params(ITableExtension::class)->setOrder('rank ASC');


		$params = $this->addHasMany(DescriptorFactory::create(Tables\RealtyListRealtyListType::class, 'realty_list_realty_list_type', 'Typ'));
		$params = $this->addHasMany(DescriptorFactory::create(Tables\RealtyListRealtyListLocation::class, 'realty_list_realty_list_location', 'Lokalita'));
		$params = $this->addHasMany(DescriptorFactory::create(Tables\RealtyListRealtyListDisposition::class, 'realty_list_realty_list_disposition', 'Dispozice'));
		$params = $this->addHasMany(DescriptorFactory::create(Tables\RealtyListRealtyListSize::class, 'realty_list_realty_list_size', 'Velikost'));


		// $this->addHasMany(DescriptorFactory::create(Tables\ArticleReadAlso::class, 'article_read_also', 'Související články'));
		// $this->addHasMany(DescriptorFactory::create(Tables\ArticleArticleTag::class, 'article_article_tag', 'Štítky'));
		
		// if (class_exists('\Andweb\Datatypes\Descriptors\Tables\ProductArticle'))
		// 	$this->addHasMany(DescriptorFactory::create(Tables\ProductArticle::class, 'product', 'Související produkty'));


	}

}