<?php

namespace App\FrontModule\Presenters;

use Nette,
Andweb,
App;

use App\FrontModule\Model\PopoUser;


class BasePresenter extends App\Presenters\BasePresenter
{
	protected $mobileDetect;

	/**
	 * @var PopoUser $popoUser
	 * @inject
	 */
	public $popoUser;

	public function startup()
	{
		parent::startup();

		$storage = $this->getUser()->getStorage();
		$storage->setNamespace("Front");

		if (isset($this['adSearch'])) {
			$this['adSearch']->onSearch[] = function ($o, $q) {
				if ($q) {
					$presenter = $this->getPresenterInfo();

					$this->redirect('Ad:default', ['navId' => 740, 'q' => $q]);

					/*
					// ad default ok na tu samou
					if ($presenter['name'] == 'front.ad' && $presenter['action'] == 'default') {
					$this->redirect('Ad:default', ['navId' => $this->presenter->navigation->navItem['id'], 'q' => $q]);
					} else {
					$this->redirect('Ad:default', ['navId' => 740, 'q' => $q]);
					}
					*/
				}
			};

		}





		// Prihlaseni / Odhlaseni / LOGOVANI
		$this->user->onLoggedIn[] = function () {
			$this->popoUser->saveLogin($this->getUser()->getId());
		};

		$this->user->onLoggedOut[] = function () {
			$this->popoUser->saveLogout($this->getUser()->getId());
		};

		if ($this->user->isLoggedIn()) {
			$url = $this->getHttpRequest()->getUrl();
			$this->popoUser->saveLog($this->getUser()->getId(), (string) $url);
		}


		// nav item
		$navItem = $this->navigation->navItem;

		// title a desc konkretni nav polozky
		$title = $navItem['page_title'] ? $navItem['page_title'] : $navItem['title'];
		$description = strip_tags($navItem['page_description']);
		$image = null;
		if ($navItem['image_id'] && $navItem->image) {
			$image = $navItem->image;
		}

		// lokalizace
		$locale = 'cs_CZ';

		$title = Nette\Utils\Strings::length($title) > 60 ? Nette\Utils\Strings::substring($title, 0, 57) . '...' : $title;
		$description = Nette\Utils\Strings::length($description) > 160 ? Nette\Utils\Strings::substring($description, 0, 157) . '...' : $description;

		// FB + Common
		$this['metaHeaders']->setHeader('og:locale', $locale);
		$this['metaHeaders']->setHeader('og:type', 'website');
		$this['metaHeaders']->setHeader('og:site_name', 'Pilotak.cz - letecký bazar a inzerce');

		$this['metaHeaders']->setHeader('og:title', $title);
		$this['metaHeaders']->setHeader('og:description', $description);
		$this['metaHeaders']->setHeader('og:url', $this->link('//this'));
		if ($image) {
			$this['metaHeaders']->setHeader("og:image", $image->getImageUrl('//800x'));
		} else {
			$this['metaHeaders']->setHeader('og:image', "https://{$_SERVER['SERVER_NAME']}/assets/gfx/fb-image.jpg");
		}

		// Twitter
		$this['metaHeaders']->setHeader('twitter:card', 'summary');
		$this['metaHeaders']->setHeader('twitter:title', $title);
		$this['metaHeaders']->setHeader('twitter:description', $description);
		if ($image) {
			$this['metaHeaders']->setHeader("og:image", $image->getImageUrl('//800x'));
		} else {
			$this['metaHeaders']->setHeader('twitter:image', "https://{$_SERVER['SERVER_NAME']}/assets/gfx/fb-image.jpg");
		}


		$this->mobileDetect = new \Mobile_Detect;

	}

	public function isMobile()
	{
		return $this->mobileDetect->isMobile();
	}

	public function isDesktop()
	{
		return !$this->isMobile();
	}

	protected function ajaxRedraw()
	{

	}
}