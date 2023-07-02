<?php

namespace App\FrontModule\Presenters;

use Nette,
Andweb,
App;

use App\FrontModule\Model;
use Andweb\Model\Mail;


class PilotakOrderPresenter extends FrontPresenter
{

	/**
	 * @var Model\OrderPilotakFacade
	 * @inject
	 */
	public $order;


	/**
	 * @var Model\RecaptchaValidationV3
	 * @inject
	 */
	public $recaptchaValidationV3;

	/**
	 * @var Model\RecaptchaValidationV3Config
	 * @inject
	 */
	public $recaptchaValidationV3Config;

	/**
	 * @var Mail
	 * @inject
	 */
	public $mail;

	
	public function actionDefault()
	{

	}



	protected function createComponentOrderForm()
	{
		$form = new Nette\Application\UI\Form;
		$form->setTranslator($this->presenter->translator);

		$form->addHidden('g_recaptcha_response')
			->getControlPrototype()
			->setAttribute('id', 'g_recaptcha_response')
			->setAttribute('data-site-key', $this->recaptchaValidationV3Config->getSiteKey());

		$form->addHidden('action')
			->setValue('validate_captcha');


		$form->addText('fullname', 'Jméno a příjmení:')
			->setRequired('Zadejte jméno a příjmení');


		$form->addText('phone', 'Telefon:')
			->setRequired('Zadejte telefon');

		$form->addText('email', 'E-mail:')
			->setRequired('Zadejte e-mailovou adresu')
			->addRule($form::EMAIL, 'Prosím zkontrolujte překlepy v e-mailové adrese');

		$form->addText('street', 'Ulice a číslo:')
			->setRequired('Zadejte Ulici a číslo');
		
		$form->addText('city', 'Město nebo obec:')
			->setRequired('Zadejte město nebo obec');

		$form->addText('zip', 'PSČ:')
			->setRequired('Zadejte PSČ');	

		$form->addText('gift_name', 'Bude to dárek pro:')
			->getControlPrototype()
			->setAttribute('placeholder', 'Jméno obdarovaného');

		$airport = $form->addSelect('airport_id', 'Letiště:')
			->setItems($this->order->listAirport())
			->setPrompt('-Vyberte-')
			->setRequired('Vyberte letiště');

		$airplane = $form->addSelect('airplane_id', 'Letadlo / Vrtulník:')
			->setHtmlAttribute('data-depends', $airport->getHtmlName())
			->setHtmlAttribute('data-items', $this->order->listAirplane())
			->setRequired('Vyberte letadlo / vrtulník')
			->addRule($form::NOT_EQUAL, 'Vyberte letadlo / vrtulník !', 0);

		
		$aa = $form->addSelect('airport_airplane_id', 'Doba letu / cena:')
			->setHtmlAttribute('data-depends', $airplane->getHtmlName())
			->setHtmlAttribute('data-items', $this->order->listFlights())
			->setRequired('Vyberte dobu letu')
			->addRule($form::NOT_EQUAL, 'Vyberte dobu letu !', 0);


		$form->addSelect('payment', 'Typ platby:')
			->setRequired('Vyberte typ platby')
			->setHtmlAttribute('data-depends', $aa->getHtmlName())
			->setHtmlAttribute('data-items', $this->order->listPayment())
			//->setItems(Model\OrderPilotakFacade::$listPayment)
			->addRule($form::NOT_EQUAL, 'Vyberte typ platby !', 0);

		$form->addTextArea('message', 'Chcete se na něco zeptat?');

		$form->addCheckbox('terms', 'Terms')
			->setRequired('Pro odeslání formulář je nutné souhlasit s obchodními podmínkami');

		$form->addSubmit('send', 'Odeslat objednávku');


		$form->onValidate[] = [$this, 'validate'];
		$form->onSuccess[] = [$this, 'save'];

		return $form;
		
	}


	public function validate($form)
	{
		try {

			$this->recaptchaValidationV3->validate($form->getValues(true));

		} catch (\Exception $e) {
			$form->addError($e->getMessage());
		}
	}


	public function save($form)
	{
		$values = $form->getValues(true);
	}

}