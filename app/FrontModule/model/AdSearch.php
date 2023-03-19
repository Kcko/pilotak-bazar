<?php

namespace App\FrontModule\Model;

use Nette,
Andweb;

use Andweb\Database\Context;

class AdSearch
{
	use FulltextWildcardsFixer;

	/**
	 * @var Context
	 */
	protected $connection;

	/**
	 * @var string|null
	 */
	private $q;


	public function __construct(Context $connection)
	{
		$this->connection = $connection;
	}


	public function setPhrase($q)
	{
		$this->q = $this->parseQuery($q);
	}



	public function parseQuery($q)
	{
		$q = $this->wildcardsFix($q);
		$q = strip_tags($q);
		$q = explode(' ', $q);
		$q = array_filter($q, function ($value) {
			if ($value)
				return true;
		});

		$q = array_map(function ($v) {
			return $v . '*';
		}, $q);
		//\Tracy\Debugger::barDump($q);
		return implode(',', $q);
	}




	// document
	public function searchBoards($limit = FALSE, $offset = 0)
	{

		$selection = $this->connection->table('pp_board');
		$selection->select('*');
		$selection->where('is_deleted IS NULL OR is_deleted = 0');

		$selection->select(
			'MATCH(name, description, description_full) AGAINST (? IN BOOLEAN MODE) AS score',
			$this->q
		);

		$selection->where(
			'MATCH(name, description, description_full) AGAINST (? IN BOOLEAN MODE)',
			$this->q
		);

		//$selection->where('id', 435);

		$selection->order(
			'5 * MATCH(name) AGAINST (? IN BOOLEAN MODE)
				+  MATCH(description) AGAINST (? IN BOOLEAN MODE)
				+  MATCH(description_full) AGAINST (? IN BOOLEAN MODE)
				DESC
				',
			$this->q,
			$this->q,
			$this->q
		);

		$selection->order('score DESC');
		$selection->order('published_from DESC');

		$selection->limit($limit, $offset);

		return $selection;
	}


	public function searchBoardsCount()
	{
		return $this->searchBoards()->count('*');
	}




	// document
	public function searchDocuments($limit = FALSE, $offset = 0)
	{

		$selection = $this->connection->table('pp_document');
		$selection->select('*');
		$selection->where('is_deleted IS NULL OR is_deleted = 0');

		$selection->select(
			'MATCH(name, description, description_full) AGAINST (? IN BOOLEAN MODE) AS score',
			$this->q
		);

		$selection->where(
			'MATCH(name, description, description_full) AGAINST (? IN BOOLEAN MODE)',
			$this->q
		);

		$selection->order(
			'5 * MATCH(name) AGAINST (? IN BOOLEAN MODE)
				+  MATCH(description) AGAINST (? IN BOOLEAN MODE)
				+  MATCH(description_full) AGAINST (? IN BOOLEAN MODE)
				DESC
				',
			$this->q,
			$this->q,
			$this->q
		);

		$selection->order('score DESC');
		$selection->order('published_from DESC');

		$selection->limit($limit, $offset);

		return $selection;
	}


	public function searchDocumentsCount()
	{
		return $this->searchDocuments()->count('*');
	}



	// news
	public function searchNews($limit = FALSE, $offset = 0)
	{
		$selection = $this->connection->table('article');
		$selection->select('*');

		$selection->where('article.published IS NOT NULL')
			->where('article.published <= ?', new \DateTime())
			->where('article.navigation_id IS NOT NULL');

		$selection->where('article.navigation_id IN (?) OR :article_cat.navigation_id IN (?)', (array) 17, (array) 17);



		$selection->select(
			'MATCH(header, perex, content) AGAINST (? IN BOOLEAN MODE) AS score',
			$this->q
		);

		$selection->where(
			'MATCH(header, perex, content) AGAINST (? IN BOOLEAN MODE)',
			$this->q
		);

		$selection->order(
			'5 * MATCH(header) AGAINST (? IN BOOLEAN MODE)
			+  MATCH(perex) AGAINST (? IN BOOLEAN MODE)
			+  MATCH(content) AGAINST (? IN BOOLEAN MODE)
			DESC
			',
			$this->q,
			$this->q,
			$this->q
		);

		$selection->order('score DESC');
		$selection->order('published DESC');

		$selection->limit($limit, $offset);

		return $selection;
	}


	public function searchNewsCount()
	{
		return $this->searchNews()->count('*');
	}


	// events
	public function searchEvents($limit = FALSE, $offset = 0)
	{
		$selection = $this->connection->table('article');
		$selection->select('*');

		$selection->where('article.published IS NOT NULL')
			->where('article.published <= ?', new \DateTime())
			->where('article.navigation_id IS NOT NULL');

		$selection->where('article.navigation_id IN (?) OR :article_cat.navigation_id IN (?)', (array) 18, (array) 18);



		$selection->select(
			'MATCH(header, perex, content) AGAINST (? IN BOOLEAN MODE) AS score',
			$this->q
		);

		$selection->where(
			'MATCH(header, perex, content) AGAINST (? IN BOOLEAN MODE)',
			$this->q
		);

		$selection->order(
			'5 * MATCH(header) AGAINST (? IN BOOLEAN MODE)
			+  MATCH(perex) AGAINST (? IN BOOLEAN MODE)
			+  MATCH(content) AGAINST (? IN BOOLEAN MODE)
			DESC
			',
			$this->q,
			$this->q,
			$this->q
		);

		$selection->order('score DESC');
		$selection->order('event_start DESC');

		$selection->limit($limit, $offset);

		return $selection;
	}


	public function searchEventsCount()
	{
		return $this->searchEvents()->count('*');
	}



	// firm
	public function searchFirms($limit = FALSE, $offset = 0)
	{
		$selection = $this->connection->table('pp_firm');
		$selection->select('*');

		$selection->select(
			'MATCH(name, description, address, contact) AGAINST (? IN BOOLEAN MODE) AS score',
			$this->q
		);

		$selection->where(
			'MATCH(name, description, address, contact) AGAINST (? IN BOOLEAN MODE)',
			$this->q
		);

		$selection->order(
			'5 * MATCH(name) AGAINST (? IN BOOLEAN MODE)
			+  MATCH(description) AGAINST (? IN BOOLEAN MODE)
			+  MATCH(address) AGAINST (? IN BOOLEAN MODE)
			+  MATCH(contact) AGAINST (? IN BOOLEAN MODE)
			DESC
			',
			$this->q,
			$this->q,
			$this->q,
			$this->q
		);

		$selection->order('score DESC');
		$selection->limit($limit, $offset);

		return $selection;
	}


	public function searchFirmsCount()
	{
		return $this->searchFirms()->count('*');
	}



	// magazine
	public function searchMagazines($limit = FALSE, $offset = 0)
	{
		$selection = $this->connection->table('pp_magazine')
			->where('year = ? OR month = ?', $this->q, $this->q)
			->order('year DESC, month DESC')
			->limit($limit, $offset);

		return $selection;
	}

	public function searchMagazinesCount()
	{
		return $this->searchMagazines($this->q)->count('*');
	}



	// place
	public function searchPlaces($limit = FALSE, $offset = 0)
	{
		$selection = $this->connection->table('pp_place');
		$selection->select('*');

		$selection->select(
			'MATCH(name, description) AGAINST (? IN BOOLEAN MODE) AS score',
			$this->q
		);

		$selection->where(
			'MATCH(name, description) AGAINST (? IN BOOLEAN MODE)',
			$this->q
		);

		$selection->order(
			'5 * MATCH(name) AGAINST (? IN BOOLEAN MODE)
			+  MATCH(description) AGAINST (? IN BOOLEAN MODE)
			DESC
			',
			$this->q,
			$this->q
		);

		$selection->order('score DESC');
		$selection->limit($limit, $offset);

		return $selection;
	}


	public function searchPlacesCount()
	{
		return $this->searchPlaces()->count('*');
	}


	// pages / static_content
	public function searchPages($limit = FALSE, $offset = 0)
	{
		$selection = $this->connection->table('static_content');

		$selection->select(
			'MATCH(content) AGAINST (? IN BOOLEAN MODE) AS score',
			$this->q
		);

		$selection->where(
			'MATCH(content) AGAINST (? IN BOOLEAN MODE)',
			$this->q
		);

		$selection->where('navigation_id IS NOT NULL');

		$selection->order(
			' MATCH(content) AGAINST (? IN BOOLEAN MODE)
			DESC
			',
			$this->q
		);

		$selection->limit($limit, $offset);

		return $selection;
	}

	public function searchPagesCount()
	{
		return $this->searchPages($this->q)->count('*');
	}


	// navigation
	public function searchNavigations($limit = FALSE, $offset = 0)
	{
		$selection = $this->connection->table('navigation');

		$selection->select(
			'MATCH(title) AGAINST (? IN BOOLEAN MODE) AS score',
			$this->q
		);

		$selection->where(
			'MATCH(title) AGAINST (? IN BOOLEAN MODE)',
			$this->q
		);

		//$selection->where('navigation_id IS NOT NULL');
		//$selection->where('redirect__navigation_id IS NULL');
		$selection->where('action IS NULL');
		$selection->where('alias !=? OR alias IS NULL', 'e404');

		$selection->order(
			' MATCH(title) AGAINST (? IN BOOLEAN MODE)
			DESC
			',
			$this->q
		);

		$selection->limit($limit, $offset);

		return $selection;
	}

	public function searchNavigationsCount()
	{
		return $this->searchNavigations($this->q)->count('*');
	}



	// teams
	public function searchTeams($limit = FALSE, $offset = 0)
	{
		$selection = $this->connection->table('our_team');
		$selection->select('*');

		$selection->select(
			'MATCH(first_name, last_name) AGAINST (? IN BOOLEAN MODE) AS score',
			$this->q
		);

		$selection->where(
			'MATCH(first_name, last_name) AGAINST (? IN BOOLEAN MODE)',
			$this->q
		);

		$selection->order(
			'MATCH(first_name) AGAINST (? IN BOOLEAN MODE)
			+  MATCH(last_name) AGAINST (? IN BOOLEAN MODE)
			DESC
			',
			$this->q,
			$this->q
		);

		$selection->order('score DESC');
		$selection->limit($limit, $offset);

		return $selection;
	}


	public function searchTeamsCount()
	{
		return $this->searchTeams()->count('*');
	}


}