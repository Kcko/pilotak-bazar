<?php

namespace App\FrontModule\Presenters;

use Nette,
Andweb,
App;

use App\FrontModule\Model;


class AirportPresenter extends FrontPresenter
{

	/**
	 * @var Model\Airport
	 * @inject
	 */
	public $airport;

	
	public function actionDefault()
	{

	}

	public function actionDetail($navParam)
	{
		// basic ad info
		$airport = $this->template->airport = $this->airport->getById($navParam);

		if (!$this->template->airport) {
			$this->error();
		}


		$this['airportRoute']->setAirport($airport->id);
		$this['flightOffers']->setAirport($airport->id);
		

		$image = null;
		if ($airport->image_id)
			$image = $airport->image->getImageUrl('//facebook');

		$this['metaHeaders']->setHeader("og:type", "article");
		$this['metaHeaders']->setHeader("og:title", $airport->name . ' (Pilotak.cz)');
		$this['metaHeaders']->setHeader("og:description", strip_tags($airport->basic_info));

		if ($image) {
			$this['metaHeaders']->setHeader("og:image", $image);
			$this['metaHeaders']->setHeader("twitter:image", $image);
		}

		$this['metaHeaders']->setHeader("twitter:card", "summary");
		$this['metaHeaders']->setHeader("twitter:title", $airport->name . ' (Pilotak.cz)');
		$this['metaHeaders']->setHeader("twitter:description", strip_tags($airport->basic_info));

	}

}