<?php

namespace App\FrontModule\Model;

use Nette,
Andweb;

use Andweb\Database\Context;

class Ad
{

	/**
	 * @var Context
	 */
	protected $connection;


	public function __construct(Context $connection)
	{
		$this->connection = $connection;
	}


	public function getList(array $options = [], $limit = false, $offset = 0)
	{
		//\Tracy\Debugger::barDump($filters, 'filters');
		$selection = $this->connection->table('ad');
		$selection->where('is_visible', 1);
		$selection->where('expiration >', new \DateTime);
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


	public function count(array $options = [])
	{
		return $this->getList($options)->count('*');
	}


	public function countRelated(int $relatedId = 0)
	{
		return $this->getListRelated($relatedId)->count('*');
	}


	public function getById($id)
	{
		return $this->connection->table('ad')->get($id);
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


	public function getStats()
	{
		$selection = $this->connection->table('ad')
			->select('COUNT(*) cnt, ad_type_id');
		$selection->where('is_visible', 1);
		$selection->where('expiration >', new \DateTime);
		$selection->group('ad_type_id');

		return $selection->fetchPairs('ad_type_id');
	}


	public function getAdInCategoriesCount()
	{
		$selection = $this->connection->table('ad');
		$selection->select('COUNT(*) cnt, navigation_id');
		$selection->where('is_visible', 1);
		$selection->where('expiration >', new \DateTime);
		$selection->group('navigation_id');

		return $selection->fetchPairs('navigation_id', 'cnt');

	}


}