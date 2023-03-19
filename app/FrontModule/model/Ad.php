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

	/**
	 * @var AdSearch
	 */
	protected $search;


	public function __construct(Context $connection, AdSearch $search)
	{
		$this->connection = $connection;
		$this->search = $search;
	}


	public function getList(array $config = [], $limit = false, $offset = 0)
	{

		$q = null;
		if (isset($config['q']) && $config['q']) {
			$q = $this->search->parseQuery($config['q']);
		}

		// base
		$selection = $this->connection->table('ad');
		$selection->select('*');
		$selection->where('is_visible', 1);
		$selection->where('expiration >', new \DateTime);

		if ($q) {
			$q = $this->search->parseQuery($config['q']);

			$selection->select(
				'MATCH(heading, content) AGAINST (? IN BOOLEAN MODE) AS score',
				$q
			);

			$selection->where(
				'MATCH(heading, content) AGAINST (? IN BOOLEAN MODE)',
				$q
			);
		}

		// offer / demand
		if (isset($config['type'])) {
			$selection->where('ad_type_id', $config['type']);
		}


		// categories
		if (isset($config['categories']) && count($config['categories'])) {
			$selection->where('navigation_id', $config['categories']);
		}


		// order, default order

		if ($q) {
			$selection->order(
				'5 * MATCH(heading) AGAINST (? IN BOOLEAN MODE)
				+  MATCH(content) AGAINST (? IN BOOLEAN MODE) 
				DESC
				',
				$q,
				$q
			);

			$selection->order('score DESC');
		}
		$selection->order('created');


		$selection->limit($limit, $offset);

		return $selection;
	}


	public function count(array $options = [])
	{
		return $this->getList($options)->count('*');
	}


	public function getById($id)
	{
		return $this->connection->table('ad')->get($id);
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
		$selection->select('COUNT(*) cnt, navigation_id, ad_type_id');
		$selection->where('is_visible', 1);
		$selection->where('expiration >', new \DateTime);
		$selection->group('navigation_id, ad_type_id');

		$data = [];
		foreach ($selection as $row) {

			if (!isset($data[$row->navigation_id]['total'])) {
				$data[$row->navigation_id]['total'] = 0;
			}

			$data[$row->navigation_id]['total'] += $row->cnt;
			$data[$row->navigation_id][$row->ad_type_id] = $row->cnt;
		}

		return $data;
		//return $selection->fetchPairs('navigation_id', 'cnt');

	}


}