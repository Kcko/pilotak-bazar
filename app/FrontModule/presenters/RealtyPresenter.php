<?php

namespace App\FrontModule\Presenters;

use Nette,
Andweb,
App;

use App\FrontModule\Model;


class RealtyPresenter extends FrontPresenter
{

	/**
	 * @var Model\RealtyList
	 * @inject
	 */
	public $model;


	public function actionDefault()
	{

	}

	public function actionDetail($navParam)
	{

		$this->template->row = $this->model->getById($navParam);

		if (!$this->template->row) {
			$this->error();
		}

		$this['contactContent']['h2oContactForm']['form']['realtyId']
			->setDefaultValue($this->template->row->id);
	}
}