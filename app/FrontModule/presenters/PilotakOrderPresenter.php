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
		$this->template->mapFlights = $this->order->flightsMap();
		$this->template->mapPayments = $this->order->paymentMap();
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


		$payment = $form->addSelect('payment', 'Typ platby:')
			->setRequired('Vyberte typ platby')
			->setHtmlAttribute('data-depends', $aa->getHtmlName())
			->setHtmlAttribute('data-items', $this->order->listPayment())
			//->setItems(Model\OrderPilotakFacade::$listPayment)
			->addRule($form::NOT_EQUAL, 'Vyberte typ platby !', 0);

		$form->addTextArea('message', 'Chcete se na něco zeptat?');

		$form->addCheckbox('terms', 'Terms')
			->setRequired('Pro odeslání formulář je nutné souhlasit s obchodními podmínkami');

		$form->addSubmit('send', 'Odeslat objednávku');


		$form->onAnchor[] = function() use ($airport, $airplane) {
			$airplane->setItems($airport->getValue() ? $this->order->listAirplane()[$airport->getValue()] : []);
		};

		$form->onAnchor[] = function() use ($airplane, $aa) {
			$aa->setItems($airplane->getValue() ? $this->order->listFlights()[$airplane->getValue()] : []);
		};

		$form->onAnchor[] = function() use ($aa, $payment) {
			$payment->setItems($aa->getValue() ? $this->order->listPayment() : []);
		};
		

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
	
		try {

			$values = $form->getValues(true);

			list($aaId, $aaCopilot) = explode(':', $values['airport_airplane_id']);
			$isCopilot = $aaCopilot == 'copilot1';
			$airport = $this->order->airportById($values['airport_id']);
			$airplane = $this->order->airplaneById($values['airplane_id']);
			$aa = $this->order->aaById($aaId);
			$payment = $this->order->paymentById($values['payment']);
			$paymentName = $this->order->paymentNameById($values['payment']);

			$save = [
				'fullname'            => $values['fullname'],
				'email'               => $values['email'],
				'phone'               => $values['phone'],
				'street'              => $values['street'],
				'city'                => $values['city'],
				'zip'                 => $values['zip'],
				'gift_name'           => $values['gift_name'],
				'message'             => $values['message'],
				'airport_id'          => $values['airport_id'],
				'airplane_id'         => $values['airplane_id'],
				'airport_airplane_id' => $aaId,
				'copilot'             => $isCopilot ? 1 : 0,
				'created'             => new \DateTime,
				'ip'                  => $_SERVER['REMOTE_ADDR'],
				'price'               => $isCopilot ? $aa['price_copilot'] : $aa['price'],
				'price_payment'       => $payment,
			];
			$save['total_price'] = $save['price'] + $payment;

			// ulozeni
			$this->order->saveOrder($save);


			// odeslani emailu
			$params = [
				'service'             => $aa . ' / '.$aa->duration.' minut ', // sluzba slozenina
				'name'                => $save['fullname'], // fullname
				'gift'                => $save['gift_name'] ,
				'street'              => $save['street'],
				'city'                => $save['city'],
				'zip'                 => $save['zip'],
				'email'               => $save['email'],
				'phone'               => $save['phone'],
				'airport'             => $airport, // letiste
				'aircraft'            => $airplane, // letadlo
				'flight'              => $aa . ' / '.$aa->duration.' minut / ' . ($isCopilot ? ' vyhlídkový let včetně pilotování' : ' vyhlídkový let') , // vybrany let + pilotak nebo ne
				'flightPrice'         => $aa->price, // zakladni cena
				'additionalPriceName' => ($aa->price_copilot - $aa->price), // priplatek za pilotovani
				'payment'             => $paymentName, // typ platby
				'totalPrice'          => $save['total_price'], // doprava
				'comment'             => $save['message'],
			];
			
			// admin
			$message = $this->mail->getMessage('orderAdmin');
			$template = $message->getTemplate();
			$template->setParameters($params);
			$this->mail->sendMessage($message);

			// client
			$message = $this->mail->getMessage('orderClient');
			$message->addTo($values['email']);
			$template = $message->getTemplate();
			$template->setParameters($params);
			$this->mail->sendMessage($message);

			/*
			$stop();
			throw new \Exception('saving ...');
			*/

			$this->flashMessage('Objednávka úspěšně uložena, děkujeme. Brzy Vás budeme kontaktovat.', 'Success');
			$this->redirect('this');

		} catch (\Exception $e) {
			if ($e instanceof Nette\Application\AbortException) {
				throw $e;
			}

			$form->addError($this->getPresenter()->translator->translate($e->getMessage()));
		}
	}

}