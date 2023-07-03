<?php

namespace App\FrontModule\Model;

use Nette,
Andweb;

use Andweb\Database\Context;
use App\FrontModule\Model;

class OrderPilotakFacade
{

	/**
	 * @var Context
	 */
	protected $connection;

	/**
	 * Airport
	 * @var $airport
	 */
	protected $airport;


	public static $listPayment = [
		0 => '-Vyberte-',
		100 => 'Převodem / Doručení e-mailem / 0,-',
	 	101 => 'Převodem ze zahraničí / 200,-',
	];


	public function __construct(Context $connection, Airport $airport)
	{
		$this->connection = $connection;
		$this->airport = $airport;
	}


	
	public function listAirport()
	{
		return $this->connection->table('vw_flights')
			->select('DISTINCT airportId AS airportId, airportName')
			->order('airportName')
			->fetchPairs('airportId', 'airportName');
	}

	public function listAirplane()
	{
		$selection = $this->connection->table('vw_flights')
			->select('*')
			->order('airplaneName');

		$airplanes = [];
		foreach ($selection as $row) {
			$airplanes[$row->airportId][$row->airportId . ':' . $row->airplaneId] = $row->airplaneName . ' / ' . $row->person_no . ' ' . \Andweb\Datatypes\Airplane::personFormatter($row->person_no);
		}

		$items = [];
		foreach ($this->listAirport() as $airportId => $airportName) {

			if (isset($airplanes[$airportId])) {
				$items[$airportId] = array_merge(['0' => '-Vyberte-'], $airplanes[$airportId]);
			} 	
		}

		return $items;
	}

	public function listFlights()
	{
		$selection = $this->connection->table('vw_flights')
			->select('*')
			->order('rank');

		$flights = [];
		foreach ($selection as $row) {
			if (!isset($flights[$row->airport . ':' . $row->airplaneId])) {
				$flights[$row->airportId . ':'. $row->airplaneId][0] = '-Vyberte-';
			}

			$flights[$row->airportId . ':'. $row->airplaneId][$row->id . ':copilot0'] = $row->duration . ' minut / ' . $row->price . ' Kč / (vyhlídkový let)';
			if ($row->price_copilot) {
				$flights[$row->airportId . ':'. $row->airplaneId][$row->id . ':copilot1'] = $row->duration . ' minut / ' . $row->price_copilot . ' Kč / (pilotovat)';
			}
		
		}
			return $flights;
	}


	public function listPayment()
	{
		return self::$listPayment;
	}


	public function airportById($id) 
	{
		return $this->connection->table('airport')
			->where('id', $id)
			->fetch();
	}


	public function airplaneById($id) 
	{
		return $this->connection->table('airplane')
			->where('id', $id)
			->fetch();
	}


	public function aaById($id) 
	{
		return $this->connection->table('airport_airplane')
			->where('id', $id)
			->fetch();
	}

	public function paymentById($id)
	{
		return $this->paymentMap()[$id];
	}


	public function paymentNameById($id)
	{
		return self::$listPayment[$id];
	}


	public function flightsMap()
	{
		$selection = $this->connection->table('vw_flights')
			->select('*')
			->order('rank');

		$items = [];
		foreach ($selection as $row)
		{
			$items[$row->id] = [
				'price' => $row->price,
				'price_copilot' => $row->price_copilot,
			];
		}


		return $items;
	}


	public function paymentMap() 
	{
		return [
			100 => 0,
			101 => 200
		];
	}


	public function saveOrder($data)
	{
		$this->connection->table('pilotak_order')
			->save($data);
	}

}