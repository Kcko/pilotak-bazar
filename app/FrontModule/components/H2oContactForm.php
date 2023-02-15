<?php

namespace App\FrontModule\Components;

use App,
Andweb,
App\FrontModule\Model,
Nette;

class H2oContactForm extends FrontControl
{

	protected $config = [
		'template' => 'default',
	];

	/* 
	sitekey:  6LcVKxMkAAAAAG0ISI59tJjUC-8Dhxm29es_GIJu 
	secretkey: 6LcVKxMkAAAAAL3urwM7lboUZOK1T_ynA3Vn0M7j
	*/

	/**
	 * @var App\FrontModule\Model\Navigation
	 */
	protected $modelNavigation;


	/**
	 * @var App\Model\File
	 */
	protected $modelFile;

	/**
	 *
	 * @var Andweb\Model\Mail $modelMail
	 */
	protected $modelMail;


	/**
	 * @var App\FrontModule\Model\Contact
	 */
	protected $contact;


	/**
	 * @var App\FrontModule\Model\RealtyList
	 */
	protected $realtyList;


	/**
	 * @var Model\RecaptchaValidationV3
	 */
	protected $recaptchaValidationV3;

	/**
	 * @var Model\RecaptchaValidationV3Config
	 */
	protected $recaptchaValidationV3Config;


	public function __construct(
		Model\Navigation $modelNavigation,
		App\Model\File $modelFile,
		Andweb\Model\Mail $modelMail,
		Model\Contact $contact,
		Model\RealtyList $realtyList,
		Model\RecaptchaValidationV3 $recaptchaValidationV3,
		Model\RecaptchaValidationV3Config $recaptchaValidationV3Config
	)
	{
		$this->modelNavigation = $modelNavigation;
		$this->modelFile = $modelFile;
		$this->modelMail = $modelMail;
		$this->contact = $contact;
		$this->realtyList = $realtyList;
		$this->recaptchaValidationV3 = $recaptchaValidationV3;
		$this->recaptchaValidationV3Config = $recaptchaValidationV3Config;
	}


	public function createComponentForm()
	{
		$form = new Nette\Application\UI\Form();
		$form->setTranslator($this->presenter->translator);


		$form->addHidden('g_recaptcha_response')
			->getControlPrototype()
			->setAttribute('id', 'g_recaptcha_response')
			->setAttribute('data-site-key', $this->recaptchaValidationV3Config->getSiteKey());

		$form->addHidden('action')
			->setValue('validate_captcha');


		$form->addText('fullname', 'Jméno')
			->addRule($form::FILLED, 'Vyplňte pole %label');

		$form->addText('email', 'E-mailová adresa')
			->addRule($form::FILLED, 'Vyplňte pole %label')
			->addRule($form::EMAIL, 'Pole %label není ve správném formátu');

		$form->addText('phone', 'Telefon');


		$form->addTextarea('message', 'Vaše zpráva');
		$form->addHidden('realtyId');

		$form->addSubmit('send', 'Odeslat');

		$form->onSuccess[] = [$this, 'handleForm'];
		$form->onValidate[] = [$this, 'validate'];
		$form->onSubmit[] = function () {
			$this->redrawControl();
		};

		return $form;

	}


	public function validate($form)
	{
		try {
			$this->recaptchaValidationV3->validate($form->getValues(true));
		} catch (\Exception $e) {
			$form->addError($e->getMessage());
			$this->redrawControl();
		}
	}


	public function handleForm($form)
	{
		$values = $form->getValues();

		$data = [
			'fullname' => $values->fullname,
			'email' => $values->email,
			'phone' => $values->phone,
			'message' => $values->message,
			'sended' => new \DateTime,
			'ipaddress' => $_SERVER['REMOTE_ADDR'],
			'realty_list_id' => $values->realtyId ? $values->realtyId : null,
		];

		$newRow = $this->contact->saveData($data);

		/**
		 * @var Nette\Mail\Message
		 */
		$message = $this->modelMail->getMessage('contact');
		$message->addTo($values->email);

		// // kopie podle kategorie
		// $emails = $this->contact->getEmailsArray($category->emails);
		// foreach ($emails as $email) {
		// 	$message->addBcc($email);
		// }

		$template = $message->getTemplate();
		$template->setParameters([
			'fullname' => $values->fullname,
			'email' => $values->email,
			'phone' => $values->phone,
			'message' => $values->message,
			'realty' => $values->realtyId ? $this->realtyList->getById($values->realtyId)->heading : null
		]);



		$this->modelMail->sendMessage($message);
		$this->flashMessage('Děkujeme. Ozveme se Vám co nejdříve.', 'Success');

		if ($this->getPresenter()->isAjax()) {
			$form->setValues([], true);
			//$this->presenter->redrawControl('recaptcha');
			$this->redrawControl();
		} else {
			$this->redirect('this');
		}
	}


	public function renderDefault(array $config = array())
	{
		$config = $this->getCurrentConfig($config);
		// $config = $this->getCurrentConfig([], TRUE);
		$this->render($config);
	}


	public function handleRedraw()
	{
		$this->redrawControl();
	}
}