<?php

namespace App\FrontModule\Model;

use Nette,
Andweb;

use Andweb\Database\Context;

class AirportRoute
{

	/**
	 * @var Context
	 */
	protected $connection;


	public function __construct(Context $connection)
	{
		$this->connection = $connection;
	}


	public function getList($airportId)
	{
		return $this->connection->table('airport_route')
			->where('airport_id', $airportId)
			->order('rank');
	}


	public function getCount($airportId)
	{
		return $this->connection->table('airport_route')
			->where('airport_id', $airportId)
			->count('*');
	}


}