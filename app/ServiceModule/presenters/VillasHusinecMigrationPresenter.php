<?php

namespace App\ServiceModule\Presenters;

use Nette,
Model,
Andweb,
App,
Nette\Application;

use Andweb\Database\Context;

class VillasHusinecMigrationPresenter extends App\Presenters\BasePresenter
{

	/**
	 * @var Andweb\Database\Context
	 */
	public $connection;

	private $map = [
		1 => 5,
		2 => 6,
		3 => 7,
		4 => 8,
		5 => 9,
		6 => 10,
		7 => 11,
		8 => 12,
		9 => 13,
		10 => 14,
		11 => 15,
		12 => 16,
		13 => 17,
	];


	public function __construct(Andweb\Database\Context $connection)
	{
		$this->connection = $connection;
	}


	public function actionTexts()
	{
		$vilas = $this->connection->table('vila');

		$data = [];
		foreach ($vilas as $vila) {
			$data[$this->map[$vila->id]] = $vila;
		}

		foreach ($data as $id => $row) {
			$this->connection->table('realty_list_lang')
				->where('realty_list_id', $id)
				->update([
					'short_content' => $row['text_top'],
					'long_content' => $row['text_bottom'],
				]);
		}

		$this->terminate();
	}


	public function actionParamsMain()
	{
		$vilas = $this->connection->table('vila');

		$data = [];
		foreach ($vilas as $vila) {
			$data[$this->map[$vila->id]] = $vila;
		}

		foreach ($data as $vilaId => $vila) {

			$paramsToSave = [];

			$paramsToSave['Obytná plocha'] = $vila->plocha . ' m2';
			if ($vila->atrium) {
				$paramsToSave['Atrium'] = $vila->atrium . ' m2';
			} else {
				$paramsToSave['Garáž'] = $vila->garaz;
			}

			$paramsToSave['Pozemek'] = $vila->pozemek . ' m2';
			$paramsToSave['Terasy'] = $vila->terasa . ' m2';

			$paramsToSave['Dispozice'] = $vila->dispozice;
			$paramsToSave['Pracovna'] = $vila->pracovna;

			$paramsToSave['Ložnice'] = $vila->loznice;
			$paramsToSave['Parkovací stání'] = $vila->parkovaci_stani;

			$paramsToSave['Koupelny'] = $vila->koupelna . ' m2';
			$paramsToSave['PENB'] = $vila->penb;

			$paramsToSave['Obýv. pokoj s kuchyní'] = $vila->obyvak_kuchyn . ' m2';
			$paramsToSave['Cena nemovitosti'] = $vila->cena;

			$rank = 1;
			foreach ($paramsToSave as $name => $value) {
				$this->connection->table('realty_list_param_main')->insert([
					'realty_list_id' => $vilaId,
					'name' => $name,
					'value' => $value,
					'rank' => $rank++,

				]);

				$this->connection->table('realty_list_lang')->where('realty_list_id', $vilaId)
					->update([
						'subheading' => implode(', ', ['Obytná plocha ' . $paramsToSave['Obytná plocha'], 'pozemek ' . $paramsToSave['Pozemek']]),
					]);


			}

		}

		// foreach ($data as $vilaId => $row) {
		// 	$params = explode("\n", $row->params_rest);

		// 	$rank = 1;
		// 	foreach ($params as $paramName) {
		// 		$this->connection->table('realty_list_param_rest')->insert([
		// 			'realty_list_id' => $vilaId,
		// 			'name' => $paramName,
		// 			'rank' => $rank++,
		// 		]);
		// 	}

		// }

		$this->terminate();
	}


	public function actionParamsRest()
	{
		$vilas = $this->connection->table('vila');

		$data = [];
		foreach ($vilas as $vila) {
			$data[$this->map[$vila->id]] = $vila;
		}

		foreach ($data as $vilaId => $row) {
			$params = explode("\n", $row->params_rest);

			$rank = 1;
			foreach ($params as $paramName) {
				$this->connection->table('realty_list_param_rest')->insert([
					'realty_list_id' => $vilaId,
					'name' => $paramName,
					'rank' => $rank++,
				]);
			}

		}

		$this->terminate();
	}



}