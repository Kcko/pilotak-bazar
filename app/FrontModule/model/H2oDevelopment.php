<?php

namespace App\FrontModule\Model;

use Nette,
Andweb;

use Andweb\Database\Context;

class H2oDevelopment
{

	/**
	 * @var Context
	 */
	protected $connection;


	public function __construct(Context $connection)
	{
		$this->connection = $connection;
	}


	public function getList($limit = FALSE, $offset = 0)
	{
		$selection = $this->connection->table('development_list');
		$selection->order('rank DESC');
		$selection->limit($limit, $offset);

		return $selection;
	}

	public function getListOnHomepage($limit = FALSE, $offset = 0)
	{
		$selection = $this->connection->table('development_hp');
		$selection->where('id', 1);
		$selection->limit($limit, $offset);

		return $selection;
	}


	public function count()
	{
		return $this->getList()->count('*');
	}
}