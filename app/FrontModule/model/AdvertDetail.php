<?php

namespace App\FrontModule\Model;

use Nette,
Andweb;

use Andweb\Database\Context;

class AdvertDetail
{

	/**
	 * @var Context
	 */
	protected $connection;


	public function __construct(Context $connection)
	{
		$this->connection = $connection;
	}


	public function listAdverts($limit = 10)
	{
		return $this->connection->table('advert_detail')
			->order('rank')
			->limit($limit);
	}

}