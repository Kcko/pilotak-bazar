<?php

namespace App\FrontModule\Model;

use Nette,
Andweb;

use Andweb\Database\Context;
use App\FrontModule\Model\NavigationWorker;


class Ad
{

	const EXPIRATION_IN_DAYS = 365;


	/**
	 * @var Context
	 */
	protected $connection;

	/**
	 * @var AdSearch
	 */
	protected $search;

	/**
	 * @var NavigationWorker
	 */
	protected $navigationWorker;

	/**
	 * @var Nette\Http\Request
	 */
	protected $httpRequest;


	public function __construct(Context $connection, AdSearch $search, NavigationWorker $navigationWorker, Nette\Http\Request $httpRequest)
	{
		$this->connection = $connection;
		$this->search = $search;
		$this->navigationWorker = $navigationWorker;
		$this->httpRequest = $httpRequest;
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

		if (isset($config['user_id']) && $config['user_id'] && isset($config['liked']) && $config['liked']) {
			$selection->where(':ad_fav.user_id', $config['user_id']);
		}

		if (isset($config['user_id']) && $config['user_id'] && isset($config['my']) && $config['my']) {
			$selection->where('user_id', $config['user_id']);
		}

		if (isset($config['filters']) && isset($config['filters']['priceFrom']) && $config['filters']['priceFrom']) {
			$selection->where('price >=', (int) $config['filters']['priceFrom']);
		}

		if (isset($config['filters']) && isset($config['filters']['priceTo']) && $config['filters']['priceTo']) {
			$selection->where('price <=', (int) $config['filters']['priceTo']);
		}

		if (isset($config['filters']) && isset($config['filters']['county']) && $config['filters']['county']) {
			$selection->where('county_id', (int) $config['filters']['county']);
		}


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
		if (isset($config['filters']) && isset($config['filters']['order']) && $config['filters']['order']) {
			switch ($config['filters']['order']) {
				case 'newest':
					$selection->order('created DESC');
					break;
				case 'priceAsc':
					$selection->order('price ASC');
					break;
				case 'priceDesc':
					$selection->order('price DESC');
					break;
			}
		} else {
			$selection->order('created DESC');
		}


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

		$selection->limit($limit, $offset);

		return $selection;
	}


	public function count(array $options = [])
	{
		return $this->getList($options)->count('*');
	}


	public function getById($id)
	{
		return $this->connection->table('ad')->where('id', $id)->fetch();
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


	public function likeAd($id, $userId)
	{
		try {
			$this->connection->table('ad_fav')->insert([
				'ad_id' => $id,
				'user_id' => $userId,
				'added' => new \DateTime
			]);
		} catch (\Exception $e) {

		}
	}

	public function dislikeAd($id, $userId)
	{
		$this->connection->table('ad_fav')
			->where('ad_id', $id)
			->where('user_id', $userId)
			->delete();
	}


	public function allLikesByUser($userId)
	{
		return $this->connection->table('ad_fav')
			->where('user_id', $userId)
			->order('added DESC')
			->fetchPairs('ad_id', 'ad_id');
	}


	public function isLiked($id, $userId)
	{
		return $this->connection->table('ad_fav')
			->where('ad_id', $id)
			->where('user_id', $userId)
			->fetch();
	}

	public function listCounty()
	{
		$rows = $this->connection->table('county')->order('country_id ASC, name ASC');

		$countyList = [];
		foreach ($rows as $row) {
			$countyList[$row->country->name][$row->id] = $row->name;
		}

		return $countyList;
	}


	public function listCategories()
	{
		$rows = $this->connection->table('navigation')->where('parent__navigation_id', 740)->order('rank');

		$list = [];
		foreach ($rows as $row) {

			$children = $this->connection->table('navigation')->where('parent__navigation_id', $row->id)->order('rank');
			foreach ($children as $child) {
				$list[$row->title][$child->id] = $child->title;

			}
		}

		return $list;
	}



	public function saveAd(array $data = [], $editRow = null, $createNavigation = false): Nette\Database\Table\ActiveRow
	{
		if ($editRow) {
			$editRow->update($data);
		} else {
			$editRow = $this->connection->table('ad')->insert($data);
		}

		if ($createNavigation) {
			$oldParentNavigationId = NULL;
			$parentNavigationId = 735; /*$editRow['navigation_id'];*/

			$this->navigationWorker->createOrUpdateNavItem(
				$oldParentNavigationId,
				$parentNavigationId,
				trim(preg_replace('/\s+/', ' ', $editRow->heading)), // odstraneni new lines z nadpisu clanku
				'detail',
				$editRow->id,
				NULL, //presenter id
				$editRow->content, // page description
				null
			);
		}

		return $editRow;
	}


	public function isEmailExists($email)
	{
		return $this->connection->table('user')
			->where('email', $email)
			->fetch();
	}



}