<?php

namespace App\FrontModule\Presenters;

use Nette,
Andweb,
App;

use App\FrontModule\Model;
use App\FrontModule\Model\NavigationWorker;


class AddAdPresenter extends FrontPresenter
{


	/**
	 * @var Model\Ad
	 * @inject
	 */
	public $model;

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
	 * Navigation worker
	 *
	 * @inject
	 * @var NavigationWorker
	 */
	public $navigationWorker;



	public function startup()
	{
		parent::startup();
	}


	public function actionDefault($q = null, $f = [])
	{

	}

	public function handleUploadFiles()
	{
		\Tracy\Debugger::barDump($_FILES, 'files');
		\Tracy\Debugger::barDump($_POST, 'post');

		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Credentials: true");
		header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
		header('Access-Control-Max-Age: 1000');
		header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

		$this->payload->state = 'OK';
		$this->payload->P = $_FILES;
		$this->payload->P2 = ['file' => [
			'name' => 'file' . rand(1, 9999),
			'size' => rand(500, 3000),
			'url' => '/assets/gfx/hp-hero.jpg'
		]];

		$this->sendPayload();
	}


	public function handleSaveOrder(array $data = [])
	{
		\Tracy\Debugger::barDump($data, 'order');
		$this->payload->state = 'OK';
		$this->payload->order = $data;
		$this->sendPayload();
	}


	protected function createComponentAddForm()
	{
		$form = new Nette\Application\UI\Form;

		$form->addHidden('g_recaptcha_response')
			->getControlPrototype()
			->setAttribute('id', 'g_recaptcha_response')
			->setAttribute('data-site-key', $this->recaptchaValidationV3Config->getSiteKey());

		$form->addHidden('action')
			->setValue('validate_captcha');


		$form->addSelect('add_type_id', 'Typ inzerátu:', [
			1 => 'Nabízím',
			2 => 'Prodám',
		])->setDefaultValue(1);


		$form->addText('heading', 'Název inzerátu:')
			->setRequired('Zadejte název')
			->addRule($form::MAX_LENGTH, 50, 'Povoleno je maximálně 50 znaků');


		$form->addTextArea('content', 'Detailní popis inzerátu:')
			->setRequired('Zadejte název')
			->addRule($form::MAX_LENGTH, 1000, 'Povoleno je maximálně 1000 znaků');

		$form->addText('price', 'Cena:')
			->setRequired(false)
			->addRule($form::INTEGER, 'Cenu zadejte prosím bez mezer');

		$form->addSelect('currency_id', 'Měna:', [
			1 => 'Kč',
			2 => '€',
		])->setDefaultValue(1);


		$form->addText('contact_email', 'Váš e-mail')
			->addRule($form::FILLED, 'Pole e-mail je povinné')
			->addRule($form::EMAIL, 'Prosím zkontrolujte překlepy v e-mailové adrese')
			->setOption('hint', 'E-mailová adresa ve formátu  neco@mail.cz');


		$form->addText('contact_phone', 'Telefon:');
		$form->addText('contact_town', 'Město:');
		$form->addSelect('county_id', 'Kraj:', $this->model->listCounty())->setPrompt('Všude');

		$form->addSelect('navigation_id', 'Kategorie:', $this->model->listCategories())->setPrompt('Vyberte kategorii')->setRequired('Vyberte kategorii');


		$form->addSubmit('send', 'Uložit');

		$form->onValidate[] = [$this, 'validate'];
		$form->onSuccess[] = [$this, 'saveAdd'];

		return $form;
	}


	public function validate($form)
	{
		try {
			$this->recaptchaValidationV3->validate($form->getValues(true), $this->recaptchaValidationV3Config->getSecretKey());
		} catch (\Exception $e) {
			$form->addError($e->getMessage());
		}
	}


	public function saveAdd($form)
	{
		$values = $form->getValues(TRUE);

		try {
			if ($this->presenter->user->getAuthenticator()->getByLogin($values['email']))
				throw new \Exception($this->presenter->translator->translate('Zadaný e-mail je již obsazen. Nezapomněli jste heslo ke svému účtu?'));

			$values['pwd'] = Andweb\Security\Authenticator::calculatePasswordHash($values['pwd']);
			$values['reg_hash'] = md5(time() . '^AK~143');
			$values['created'] = new \DateTime;

			// cloveku co se registruje
			$message = $this->mail->getMessage('lostPassword');
			unset($values['pwd1']);
			unset($values['g_recaptcha_response']);
			unset($values['action']);

			$newUserRow = $this->register->createUser($values);

			// cloveku co se registruje
			$message = $this->mail->getMessage('newRegistration');
			$message->addTo($values['email']);
			$template = $message->getTemplate();
			$template->activationLink = $this->link('//:Front:PopoUser:userActivation', $values['reg_hash']);
			$this->mail->sendMessage($message);


			// // adminovi jako notifikace
			// $message = $this->mail->getMessage('newRegistrationAdmin');
			// $template = $message->getTemplate();
			// $template->setParameters($values);
			// $template->link = $this->link('//:Back:User:AdminUser:edit', ['pKey' => (array) $newUserRow->id]);
			// $this->mail->sendMessage($message);

			$this->flashMessage('Registrace proběhla úspěšně. Nyní si prosím zkontrolujte svojí e-mailovou schránku a potvrďte registraci odkazem, který jsme Vám právě poslali.', 'Success');

		} catch (\Exception $e) {
			$form->addError($this->presenter->translator->translate($e->getMessage()));
		}
	}


}