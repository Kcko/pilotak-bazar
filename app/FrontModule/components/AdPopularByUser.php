<?php

namespace App\FrontModule\Components;

use Nette,
Andweb,
App\Components\VisualPaginator;

use App\FrontModule\Model\Navigation;
use App\FrontModule;
use Nette\Application\UI;


class AdPopularByUser extends AbstractList
{
	protected $config = [
		'template' => 'default',
		'limit' => 16,
		'paging' => true,
	];

	/**
	 * @var FrontModule\Model\Ad
	 */
	protected $model;

	/**
	 * @var Navigation
	 */
	protected $modelNavigation;


	/**
	 * @var Nette\Security\User
	 */
	protected $user;



	public function __construct(FrontModule\Model\Ad $model, Navigation $modelNavigation, Nette\Security\User $user)
	{
		$this->model = $model;
		$this->modelNavigation = $modelNavigation;
		$this->user = $user;
		parent::__construct();
	}

	public function getCurrentConfig(array $config = [], $lastConfig = FALSE)
	{
		$config = parent::getCurrentConfig($config);
		$config['user_id'] = $this->user->getId();

		return $config;
	}


	public function renderDefault(array $config = [])
	{
		$config = $this->getCurrentConfig($config);

		list($limit, $offset) = $this->getPages($config);

		$this->template->advertisements = $this->model->getList(
			$config,
			$limit,
			$offset
		);

		$this->template->config = $config;

		$this->render($config);

	}




	public function renderCnt(array $config = [])
	{
		$config = $this->getCurrentConfig($config);

		$this->template->cnt = $this->count($config);

		$this->render($config);
	}



	public function count(array $config = [])
	{
		static $cache;

		if ($cache === null) {
			$config = $this->getCurrentConfig($config);
			$cache = $this->model->count($config);
		}

		return $cache;
	}



	protected function createComponentLikeControl()
	{
		$allLikes = $this->user->getId() ? $this->model->allLikesByUser($this->user->getId()) : [];

		return new Nette\Application\UI\Multiplier(function (int $adId) use ($allLikes) {
			$control = new Like($adId, $allLikes[$adId] ?? false, $this->model, $this->user);
			$control->onLikeAction[] = function ($control) {
				$this->redrawControl('list');
			};
			return $control;

		});
	}



}