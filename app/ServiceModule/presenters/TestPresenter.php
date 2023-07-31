<?php

namespace App\ServiceModule\Presenters;

use Nette,
Model,
Andweb,
App,
Nette\Application;

use Andweb\Database\Context;

class TestApiPresenter extends App\Presenters\BasePresenter
{
	public function run(Nette\Application\Request $request)
	{
		$message = $request->getParameter('message');


		return new Nette\Application\Responses\JsonResponse(['message' => "$message"]);
	}
}

