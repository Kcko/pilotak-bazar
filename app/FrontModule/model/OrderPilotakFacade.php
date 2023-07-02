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
			$airplanes[$row->airplaneId][$row->airportId . ':' . $row->airplaneId] = $row->airplaneName . ' / ' . $row->person_no . ' ' . \Andweb\Datatypes\Airplane::personFormatter($row->person_no);
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


}