<?php

namespace App\FrontModule\Model;

use Nette,
Andweb;

use Andweb\Database\Context;

class RealtyList
{

	/**
	 * @var Context
	 */
	protected $connection;


	public function __construct(Context $connection)
	{
		$this->connection = $connection;
	}


	public function getList($filters = [], $limit = FALSE, $offset = 0)
	{
		//\Tracy\Debugger::barDump($filters, 'filters');
		$selection = $this->connection->table('realty_list');

		// filters
		if (count($filters)) {
			// type
			$type = array_filter($filters['type'], function ($k) {
				return $k != 0;
			});
			// location
			$location = array_filter($filters['location'], function ($k) {
				return $k != 0;
			});
			// disposition
			$disposition = array_filter($filters['disposition'], function ($k) {
				return $k != 0;
			});
			// size
			$size = array_filter($filters['size'], function ($k) {
				return $k != 0;
			});

			if (count($type)) {
				$selection->where(':realty_list_realty_list_size.realty_list_size_id', $type);
			}

			if (count($location)) {
				$selection->where(':realty_list_realty_list_location.realty_list_location_id', $location);
			}

			if (count($disposition)) {
				$selection->where(':realty_list_realty_list_disposition.realty_list_disposition_id', $disposition);
			}

			if (count($size)) {
				$selection->where(':realty_list_realty_list_size.realty_list_size_id', $size);
			}
		}
		$selection->order('rank DESC');
		$selection->limit($limit, $offset);

		return $selection;
	}


	public function getListRelated(int $relatedId = 0, $limit = FALSE, $offset = 0)
	{
		$selection = $this->connection->table('realty_list');
		$selection->where('id != ?', $relatedId);
		$selection->order('rank DESC');
		$selection->limit($limit, $offset);

		return $selection;
	}


	public function count($filters = [])
	{
		return $this->getList($filters)->count('*');
	}


	public function countRelated(int $relatedId = 0)
	{
		return $this->getListRelated($relatedId)->count('*');
	}


	public function getById($id)
	{
		return $this->connection->table('realty_list')->get($id);
	}



	public function filterList($type)
	{
		$selection = $this->connection->table('realty_list_' . $type)
			->order('name');

		$rows = [];
		foreach ($selection as $row) {
			$rows[$row->id] = $row->name;
		}

		return $rows;
	}


	public function filterListAvailable($type)
	{
		$list = $this->filterList($type);

		//$selection = $this->connection->table('realty_list_realty_list_' . $type);
		//$selection->select('DISTINCT realty_list_' . $type . '_id AS id');
		$selection = $this->connection->table('realty_list_' . $type);

		$availableList = [];
		foreach ($selection as $row) {
			// if (array_key_exists($row->id, $list)) {
			$availableList[$row->id] = $list[$row->id];
			// }
		}

		return $availableList;

	}


}