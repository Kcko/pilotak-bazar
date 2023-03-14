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


	public function getList(array $config = [], $limit = false, $offset = 0)
	{
		// base
		$selection = $this->connection->table('ad');
		$selection->where('is_visible', 1);
		$selection->where('expiration >', new \DateTime);

		// offer / demand
		if (isset($config['type'])) {
			$selection->where('ad_type_id', $config['type']);
		}

		// categories
		if (isset($config['categories']) && count($config['categories'])) {
			$selection->where('navigation_id', $config['categories']);
		}


		// order, default order
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
		$selection->select('COUNT(*) cnt, navigation_id');
		$selection->where('is_visible', 1);
		$selection->where('expiration >', new \DateTime);
		$selection->group('navigation_id');

		return $selection->fetchPairs('navigation_id', 'cnt');

	}


}