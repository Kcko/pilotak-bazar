<?php

namespace App\FrontModule\Presenters;

use Nette,
Andweb,
App;

use App\FrontModule\Model;


class AdPresenter extends FrontPresenter
{

	/**
	 * @var Model\Ad
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


	}
}