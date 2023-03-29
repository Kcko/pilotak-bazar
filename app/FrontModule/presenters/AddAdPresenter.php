<?php

namespace App\FrontModule\Presenters;

use Nette,
Andweb,
App;

use App\FrontModule\Model;
use App\FrontModule\Model\NavigationWorker;
use Andweb\Model\Mail;


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

	/**
	 * @var Mail
	 * @inject
	 */
	public $mail;

	/**
	 * @var string
	 */
	protected $operation = 'add';


	public function startup()
	{
		parent::startup();
	}


	// vlozen noveho i editace stavajiciho ;)
	public function actionDefault($id = null, $token = null)
	{
		$this->template->testAd = $this->model->getById(502);
	}

	public function actionDelete($id, $token = null)
	{
		$ad = $this->template->ad = $this->model->getById($id);

		try {
			if (!$ad) {
				 throw new \Exception('Tento inzerát neexistuje');
			}

			elseif ($ad->user_id && !$this->getUser()->isLoggedIn()) {
				 throw new \Exception('Přihlašte se prosím');
			}

			elseif ($ad->user_id && $this->getUser()->isLoggedIn() && $ad->token !== $token) {
				 throw new \Exception('Inzerát nelze smazat jako nepřihlášený uživatel');
			}
		}
		catch (\Exception $e) {
			$this->template->error = $e->getMessage();
		}

	
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

	public function handleDeleteConfirmation()
	{

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


		$form->addSelect('ad_type_id', 'Typ inzerátu:', [
			1 => 'Nabízím',
			2 => 'Prodám',
		])->setDefaultValue(1);


		$form->addText('heading', 'Název inzerátu:')
			->setRequired('Zadejte název')
			->addRule($form::MAX_LENGTH, 'Povoleno je maximálně 50 znaků', 50);


		$form->addTextArea('content', 'Detailní popis inzerátu:')
			->setRequired('Zadejte název')
			->addRule($form::MAX_LENGTH, 'Povoleno je maximálně 1000 znaků', 1000);

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
		$form->onSuccess[] = [$this, 'save'];

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


	public function save($form)
	{
		$values = $form->getValues(TRUE);

		try {
			// if ($this->presenter->user->getAuthenticator()->getByLogin($values['email']))
			// 	throw new \Exception($this->presenter->translator->translate('Zadaný e-mail je již obsazen. Nezapomněli jste heslo ke svému účtu?'));

			$save = [
				'ad_type_id' => $values['ad_type_id'],
				'heading' => $values['heading'],
				'content' => $values['content'],
				'navigation_id' => $values['navigation_id'],
				'price' => $values['price'],
				'currency_id' => $values['currency_id'],
				'county_id' => $values['county_id'],
				'contact_email' => $values['contact_email'],
				'contact_town' => $values['contact_town'],
				'contact_phone' => $values['contact_phone'],
				'is_visible' => 1,
			];

			if ($this->operation == 'add') {
				$save['token'] = md5(time() . '^AK~972') . '_' . date('His');
				$save['top_date'] = $save['created'] = new \DateTime;
				$save['expiration'] = new \DateTime('+' . Model\Ad::EXPIRATION_IN_DAYS . ' days');
			} else {
				$ad = new \stdClass; // TODO ... realny radek
				$save['updated'] = new \DateTime;
				$save['updated_cnt'] = $ad->updated_cnt + 1;
			}



			$ad = $this->model->saveAd($save, null, true);


			// vklada inzerat
			$message = $this->mail->getMessage('adAdded');
			$message->addTo($values['contact_email']);
			$template = $message->getTemplate();
			//$template->activationLink = $this->link('//:Front:PopoUser:userActivation', $values['reg_hash']);
			$template->adDetailUrl = 'ADD';
			$template->adEditUrl = 'EDIT';
			$template->adDeleteUrl = 'DELETE';
			$this->mail->sendMessage($message);


			// // adminovi jako notifikace
			// $message = $this->mail->getMessage('newRegistrationAdmin');
			// $template = $message->getTemplate();
			// $template->setParameters($values);
			// $template->link = $this->link('//:Back:User:AdminUser:edit', ['pKey' => (array) $newUserRow->id]);
			// $this->mail->sendMessage($message);

			$this->flashMessage('Inzerát úspěšně přidán', 'Success');

		} catch (\Exception $e) {
			$form->addError($this->presenter->translator->translate($e->getMessage()));
		}
	}







}