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

		if ($id) {
			$ad = $this->template->ad = $this->model->getById($id);
			$this->operation = 'edit';

			try {
				if (!$ad) {
					throw new \Exception('Tento inzerát neexistuje');
				} elseif ($ad->user_id && !$this->getUser()->isLoggedIn()) {
					throw new \Exception('Přihlašte se prosím');
				} elseif ($ad->user_id && $this->getUser()->isLoggedIn() && $ad->token !== $token) {
					throw new \Exception('Inzerát nelze upravit jako nepřihlášený uživatel');
				} elseif (!$this->getUser()->isLoggedIn() && $ad->token !== $token) {
					throw new \Exception('Inzerát nelze upravit, odkaz není platný');
				} elseif ($ad->is_visible === 0) {
					throw new \Exception('Inzerát neexistuje');
				}

				// vychozi hodnoty
				$this['addForm']->setDefaults($ad->toArray());

			} catch (\Exception $e) {
				$this->template->error = $e->getMessage();
			}
		} else {
			if ($this->getUser()->isLoggedIn()) {
				$identity = $this->getUser()->getIdentity();
				$this['addForm']->setDefaults([
					'contact_email' => $identity->email,
					'contact_phone' => $identity->phone
				]);
			}
		}
	}

	public function renderDefault($id = null, $token = null)
	{
		$this->template->operation = $this->operation;
	}

	public function actionDelete($id, $token = null)
	{
		$ad = $this->template->ad = $this->model->getById($id);

		try {
			if (!$ad) {
				throw new \Exception('Tento inzerát neexistuje');
			} elseif ($ad->user_id && !$this->getUser()->isLoggedIn()) {
				throw new \Exception('Přihlašte se prosím');
			} elseif ($ad->user_id && $this->getUser()->isLoggedIn() && $ad->token !== $token) {
				throw new \Exception('Inzerát nelze smazat jako nepřihlášený uživatel');
			} elseif (!$this->getUser()->isLoggedIn() && $ad->token !== $token) {
				throw new \Exception('Inzerát nelze smazat, odkaz není platný');
			} elseif ($ad->is_visible === 0) {
				throw new \Exception('Inzerát neexistuje');
			}
		} catch (\Exception $e) {
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
		if ($this->template->ad->is_visible) {
			$this->template->ad->update([
				'is_visible' => 0,
				'deleted' => new \DateTime
			]);

			$this->flashMessage('Inzerát odstraněn', 'success');
		}

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
			if (!$this->getUser()->isLoggedIn() && $this->model->isEmailExists($form->getValues(true)['contact_email'])) {
				throw new \Exception("Tento e-mail nelze použít, patří registrovanému uživateli");
			}

			$this->recaptchaValidationV3->validate($form->getValues(true), $this->recaptchaValidationV3Config->getSecretKey());

		} catch (\Exception $e) {
			$form->addError($e->getMessage());
		}
	}


	public function save($form)
	{
		$values = $form->getValues(TRUE);

		try {
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

				$ad = $this->model->saveAd($save, null, true);

			} else {
				$ad = $this->template->ad;
				$save['updated'] = new \DateTime;
				$save['updated_cnt'] = $ad->updated_cnt + 1;

				$ad = $this->model->saveAd($save, $this->template->ad, false);
			}

			// vklada inzerat
			$message = $this->mail->getMessage($this->operation == 'add' ? 'adAdded' : 'adEdited');
			$message->addTo($values['contact_email']);
			$template = $message->getTemplate();

			$template->adDetailUrl = $this->link('//:Front:Ad:detail', $ad->id);
			$template->adEditUrl = $this->link('//:Front:AddAd:default', $ad->id, $ad->token);
			$template->adDeleteUrl = $this->link('//:Front:AddAd:delete', $ad->id, $ad->token);

			$this->mail->sendMessage($message);


			// // adminovi jako notifikace
			// $message = $this->mail->getMessage('newRegistrationAdmin');
			// $template = $message->getTemplate();
			// $template->setParameters($values);
			// $template->link = $this->link('//:Back:User:AdminUser:edit', ['pKey' => (array) $newUserRow->id]);
			// $this->mail->sendMessage($message);

			if ($this->operation == 'add')
				$this->flashMessage('Inzerát úspěšně přidán', 'Success');
			else
				$this->flashMessage('Inzerát úspěšně upraven', 'Success');


			$this->redirect('Ad:detail', $ad->id);

		} catch (\Exception $e) {
			if ($e instanceof Nette\Application\AbortException) {
				throw $e;
			}

			$form->addError($this->presenter->translator->translate($e->getMessage()));
		}
	}







}