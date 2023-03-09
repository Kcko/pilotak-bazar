<?php

namespace App\FrontModule\Model;

use Nette,
Andweb;

use Andweb\Database\Context;

class MainAdvertItems
{

	/**
	 * @var Context
	 */
	protected $connection;


	public function __construct(Context $connection)
	{
		$this->connection = $connection;
	}


	public function getList()
	{
		$selection = $this->connection->table('advert_item');
		$selection->order('rank DESC');

		return $selection;
	}


	public function count()
	{
		return $this->getList()->count('*');
	}
}