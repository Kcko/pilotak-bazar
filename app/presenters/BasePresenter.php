<?php

namespace App\Presenters;

use Nette,
	App\Model,
	Andweb;


use Andweb\Presenters\Traits;
//use App\EshopModule\Model\CurrencySwitcher;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Andweb\Application\UI\Presenter
{
	use Traits\LanguageTrait;
	
	//use Traits\CurrencyTrait;
	
	use Traits\DomainTrait;
	
	/** @persistent */
	public $domain;

	/** @persistent */
	public $language;

	/**
	 * 
	 * @var CurrencySwitcher
	 * @inject
	 */
	//public $currencySwitcher;
	
	/**
	 * 
	 * @var \App\FrontModule\Model\Visitor
	 * @inject
	 */
	public $visitor;


	/**
	 * 
	 * @var Andweb\Database\Context
	 * @inject
	 */
	public $connection;

 
	/**
	 *
	 * @var \App\FrontModule\Model\RecaptchaValidationV3Config $recaptchaConfig
	 * @inject
	 */
    public $recaptchaConfig;


	public function startup()
	{
		parent::startup();

		// must be done this way, because configuration can change depends on available project modules
		//$this->initCurrency($this->currencySwitcher->getSelectedCurrencyId());
		
		// visitor tracking
		//$this->visitor->trackVisitor();
	}

	public function listImageGroups($navigationId) 
	{
		return $this->connection->table('static_content_image_group')
			->where('navigation_id', $navigationId)
			->fetchAll();
	}

	public function listFilesGroups($navigationId) 
	{
		return $this->connection->table('static_content_file_group')
			->where('navigation_id', $navigationId)
			->fetchAll();
	}
}