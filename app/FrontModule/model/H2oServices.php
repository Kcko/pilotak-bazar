<?php

namespace App\FrontModule\Model;

use Nette,
Andweb;

use Andweb\Database\Context;

class H2oServices
{

	/**
	 * @var Context
	 */
	protected $connection;


	public function __construct(Context $connection)
	{
		$this->connection = $connection;
	}


	public function getList(int $navigationId = 0)
	{
		$selection = $this->connection->table('service_list');
		$selection->where('navigation_id', $navigationId);
		$selection->order('rank DESC');

		return $selection;
	}


	public function count()
	{
		return $this->getList()->count('*');
	}
}