<?php

namespace App\FrontModule\Presenters;

use Nette,
Andweb,
App\FrontModule\Model;

use App\UserModule\Model\Register;
use App\FrontModule\Model\PopoUser;
use Andweb\Model\Mail;

class PopoUserPresenter extends FrontPresenter
{

	/**
	 * @var Register $register
	 * @inject
	 */
	public $register;

	/**
	 * @var PopoUser $popoUser
	 * @inject
	 */
	public $popoUser;

	/**
	 * @var Mail
	 * @inject
	 */
	public $mail;

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


	public function actionLogin()
	{
		if ($this->user->isLoggedIn()) {
			$this->redirectUrl('/');
		}
	}


	public function actionRegistration()
	{
		if ($this->user->isLoggedIn()) {
			$this->redirectUrl('/');
		}
	}


	public function actionLostPassword()
	{
		if ($this->user->isLoggedIn()) {
			$this->redirectUrl('/');
		}
	}


	public function actionSettings()
	{
		if (!$this->user->isLoggedIn()) {
			$this->redirectUrl('/');
		}

		$userRow = $this->popoUser->getById($this->getUser()->getId());
		$this['settingsForm']->setDefaults($userRow->toArray());
	}


	public function actionRenewPassword($hash)
	{
		try {
			if (!$row = $this->popoUser->isHashExists($hash)) {
				throw new \Exception('Autorizační token pro změnu hesla neexistuje (#1)');
			}

			if (!$this->popoUser->wasPasswordSend($row['hash_validity'])) {
				throw new \Exception('Autorizační token pro změnu hesla již není platný');
			}

		} catch (\Exception $e) {
			$this->template->errorMessage = $e->getMessage();
		}

	}


	public function actionUserActivation($hash)
	{
		if ($this->user->isLoggedIn()) {
			$this->redirectUrl('/');
		}

		try {
			if (!$row = $this->popoUser->isActivationHashExists($hash)) {
				throw new \Exception('Uživatelský profil nelze aktivovat, aktivační token není platný');
			}

			$row->update([
				'activated' => new \DateTime,
				'reg_hash' => null,
				'active' => 1,
			]);

			$this->flashMessage('Uživatelský profil aktivován, můžete se přihlásit', 'success');



		} catch (\Exception $e) {
			$this->flashMessage($e->getMessage(), 'error');
		}

	}


	public function actionLogout()
	{
		if ($this->getUser()->isLoggedIn()) {
			$this->getUser()->logout(true);
		}

		$this->redirectUrl('/');
	}


	public function renderLogin()
	{
		// mam to jako dropdown, takze ne
		$this->redirectUrl('/');
	}


	protected function createComponentRenewPasswordForm()
	{
		$form = new Nette\Application\UI\Form;


		$form->addPassword('pwd', 'Nové heslo')
			->addRule($form::FILLED, 'Pole je povinné');

		$form->addPassword('pwd1', 'Nové heslo pro kontrolu')
			->addRule($form::FILLED, "Pole je povinné")
			->addConditionOn($form["pwd1"], $form::FILLED)
			->addRule($form::EQUAL, "Hesla se musí shodovat", $form["pwd"]);

		$form->addSubmit('send', 'Změnit heslo');

		$form->onSuccess[] = [$this, 'renewPasswordSucceeded'];

		return $form;
	}



	protected function createComponentSignInForm()
	{
		$form = new Nette\Application\UI\Form;
		$form->addText('email', 'E-mailová adresa:')
			->setRequired('Zadejte e-mailovou adresu');

		$form->addPassword('password', 'Heslo:')
			->setRequired('Zadejte heslo');

		$form->addSubmit('send', 'Přihlásit se');

		$form->onSuccess[] = [$this, 'signInFormSucceeded'];

		return $form;
	}


	protected function createComponentLostPasswordForm()
	{
		$form = new Nette\Application\UI\Form;

		$form->addHidden('g_recaptcha_response')
			->getControlPrototype()
			->setAttribute('id', 'g_recaptcha_response')
			->setAttribute('data-site-key', $this->recaptchaValidationV3Config->getSiteKey());

		$form->addHidden('action')
			->setValue('validate_captcha');

		$form->addText('email', 'E-mailová adresa:')
			->setRequired('Zadejte e-mailovou adresu');

		$form->addSubmit('send', 'Odeslat');

		$form->onValidate[] = [$this, 'validate'];
		$form->onSuccess[] = [$this, 'lostPasswordSucceeded'];

		return $form;
	}


	protected function createComponentRegistrationForm()
	{
		$form = new Nette\Application\UI\Form;

		$form->addHidden('g_recaptcha_response')
			->getControlPrototype()
			->setAttribute('id', 'g_recaptcha_response')
			->setAttribute('data-site-key', $this->recaptchaValidationV3Config->getSiteKey());

		$form->addHidden('action')
			->setValue('validate_captcha');

		$form->addText('email', 'Váš e-mail')
			->addRule($form::FILLED, 'Pole e-mail je povinné')
			->addRule($form::EMAIL, 'Prosím zkontrolujte překlepy v e-mailové adrese')
			->setOption('hint', 'E-mailová adresa ve formátu  neco@mail.cz');

		$form->addPassword('pwd', 'Heslo')
			->addRule($form::FILLED, 'Pole je povinné');

		$form->addPassword('pwd1', 'Heslo pro kontrolu')
			->addRule($form::FILLED, "Pole je povinné")
			->addConditionOn($form["pwd1"], $form::FILLED)
			->addRule($form::EQUAL, "Hesla se musí shodovat", $form["pwd"]);

		$form->addText('name', 'Jméno:')
			->setRequired('Zadejte jméno');

		$form->addText('surname', 'Příjmení:')
			->setRequired('Zadejte příjmení');

		$form->addSubmit('send', 'Zaregistrovat se');

		$form->onValidate[] = [$this, 'validate'];
		$form->onSuccess[] = [$this, 'registerSucceeded'];

		return $form;
	}



	protected function createComponentSettingsForm()
	{
		$form = new Nette\Application\UI\Form;

		$form->addText('email', 'Váš e-mail')->setDisabled(true);
		$form->addText('phone', 'Kontaktní telefon');

		$form->addPassword('pwd', 'Heslo');

		$form->addPassword('pwd1', 'Heslo pro kontrolu')
			->addConditionOn($form["pwd1"], $form::FILLED)
			->addRule($form::EQUAL, "Hesla se musí shodovat", $form["pwd"]);

		$form->addText('name', 'Jméno:')
			->setRequired('Zadejte jméno');

		$form->addText('surname', 'Příjmení:')
			->setRequired('Zadejte příjmení');

		$form->addSubmit('send', 'Změnit údaje');
		$form->onSuccess[] = [$this, 'settingsSucceeded'];

		return $form;
	}



	public function renewPasswordSucceeded($form)
	{
		$values = $form->getValues(TRUE);

		try {
			$hash = $this->getParameter('hash');
			if (!$row = $this->popoUser->isHashExists($hash)) {
				throw new \Exception('Autorizační token pro změnu hesla neexistuje (#2)');
			}

			$row->update([
				'hash' => null,
				'hash_validity' => null,
				'pwd' => \Andweb\Security\Authenticator::calculatePasswordHash($values['pwd'])
			]);

			$row = $row->toArray();
			unset($row['pwd']);
			$this->user->login(new Nette\Security\Identity($row["id"], array(), $row));
			$this->flashMessage('Změna hesla proběhla v pořádku a rovnou jsme vás přihlásili.', 'Success');
			//$this->redirect('this');

		} catch (\Exception $e) {
			$form->addError($this->presenter->translator->translate($e->getMessage()));
		}
	}


	public function lostPasswordSucceeded($form)
	{
		$values = $form->getValues(TRUE);

		try {
			if (!$row = $this->popoUser->isEmailExists($values['email']))
				throw new \Exception('E-mailová adresa nebyla nalezena');

			if ($this->popoUser->wasPasswordSend($row['lost_password_sended']))
				throw new \Exception('Heslo bylo v posledních 24h již jednou zasláno, zkuste to později.');

			$token = md5(time() . '^9F~l7');
			$row->update([
				'hash' => $token,
				'hash_validity' => new \DateTime,
				'lost_password_sended' => new \DateTime,
			]);

			// cloveku co se registruje
			$message = $this->mail->getMessage('lostPassword');
			$message->addTo($values['email']);
			$template = $message->getTemplate();
			$template->link = $this->link('//:Front:PopoUser:renewPassword', $token);
			$this->mail->sendMessage($message);

			$this->flashMessage('Na zadanou e-mailovou adresu Vám zasíláme autorizační odkaz ke změně hesla. Tento odkaz má platnost 1 den.', 'Success');
			$this->redirect('this');

		} catch (\Exception $e) {
			$form->addError($this->presenter->translator->translate($e->getMessage()));
		}
	}


	public function registerSucceeded($form)
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


	public function signInFormSucceeded($form)
	{

		$this->getUser()->setExpiration('+ 20 minutes', TRUE);
		//$this->getUser()->setExpiration('+ 14 days', FALSE);

		try {
			$values = $form->getValues();
			$this->getUser()->login($values->email, $values->password);

		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());
			return;
		}

		//$this->restoreRequest($this->backlink);
		$this->flashMessage('Byl(a) jste v pořádku přihlášen(a)', 'Success');
		$this->redirect('this');
	}


	public function settingsSucceeded($form)
	{
		$values = $form->getValues(TRUE);

		try {

			if ($values['pwd']) {
				$values['pwd'] = Andweb\Security\Authenticator::calculatePasswordHash($values['pwd']);
			}

			unset($values['pwd1']);
			unset($values['g_recaptcha_response']);
			unset($values['action']);

			$userRow = $this->popoUser->getById($this->getUser()->getId());
			$userRow->update($values);

			$this->flashMessage('Změna nastavení proběhla úspěšně', 'Success');

		} catch (\Exception $e) {
			$form->addError($this->presenter->translator->translate($e->getMessage()));
		}
	}






	public function validate($form)
	{
		try {
			$this->recaptchaValidationV3->validate($form->getValues(true), $this->recaptchaValidationV3Config->getSecretKey());
		} catch (\Exception $e) {
			$form->addError($e->getMessage());
		}
	}

}