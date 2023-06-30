<?php

namespace App\FrontModule\Model;

use Nette,
Andweb;

use Andweb\Database\Context;

class FlightList
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
		return $this->connection->table('flight_list')
			->where('active', 1)
			->order('rank');
	}


	public function getById($id)
	{
		return $this->connection->table('flight_list')
			->where('id', $id)->fetch();
	}


}