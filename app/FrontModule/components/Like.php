<?php

namespace App\FrontModule\Components;

use Nette,
Andweb,
App\Components\VisualPaginator;

use App\FrontModule\Model\Navigation;
use App\FrontModule;
use Nette\Application\UI;


class Like extends FrontControl
{
	protected $config = [
		'template' => 'default',
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


	/**
	 * @var int
	 */
	protected $id;

	/**
	 * @var bool
	 */
	protected $isLiked;

	/**
	 * @var array
	 */
	public $onLikeAction = [];

	public function __construct(int $id, bool $isLiked, FrontModule\Model\Ad $model, Navigation $modelNavigation, Nette\Security\User $user)
	{
		$this->id = $id;
		$this->isLiked = $isLiked;
		$this->model = $model;
		$this->modelNavigation = $modelNavigation;
		$this->user = $user;

		parent::__construct();
	}

	public function getCurrentConfig(array $config = [], $lastConfig = FALSE)
	{
		$config = parent::getCurrentConfig($config);

		return $config;
	}


	public function renderDefault(array $config = [])
	{
		$config = $this->getCurrentConfig($config);

		$this->template->isLiked = $this->isLiked;

		$this->render($config);

	}


	public function handleLike()
	{
		$this->model->likeAd($this->id, $this->user->getId());
		$this->isLiked = $this->model->isLiked($this->id, $this->user->getId());
		$this->redrawControl();
		$this->presenter['userBoxInfo']->redrawControl('adPopularByUserCnt');

		$this->onLikeAction($this);
	}

	public function handleDislike()
	{
		$this->model->dislikeAd($this->id, $this->user->getId());
		$this->isLiked = $this->model->isLiked($this->id, $this->user->getId());
		$this->redrawControl();
		$this->presenter['userBoxInfo']->redrawControl('adPopularByUserCnt');

		$this->onLikeAction($this);
	}


}