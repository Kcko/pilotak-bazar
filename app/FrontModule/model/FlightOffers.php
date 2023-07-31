<?php

namespace App\FrontModule\Model;

use Nette,
Andweb;

use Andweb\Database\Context;

class FlightOffers
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
		$selection =  $this->connection->table('airport_airplane')
			->where('airport_id', $airportId)
			->where('airplane.active', 1)
			->where('airport_airplane.active', 1)
			->order('rank');

		$list = [];
		$airplanes = [];
		foreach ($selection as $row) {
			$list[$row->airplane_id][] = $row;
			$airplanes[$row->airplane_id] = $row->airplane;
		}

		return ['list' => $list, 'airplanes' => $airplanes];
	}


}