<?php

namespace App\FrontModule\Model;

use Nette,
Andweb;

use Andweb\Database\Context;

class PopoUser
{
	/**
	 * @var Context
	 */
	protected $connection;

	public function __construct(Context $connection)
	{
		$this->connection = $connection;
	}


	public function getById($id)
	{
		return $this->connection->table('user')
			->where('id', $id)
			->fetch();
	}

	public function isHashExists($hash)
	{
		return $this->connection->table('user')
			->select('*')
			->where('hash', $hash)
			->fetch();
	}


	public function isActivationHashExists($hash)
	{
		return $this->connection->table('user')
			->select('*')
			->where('reg_hash', $hash)
			->fetch();
	}

	public function isEmailExists($email)
	{
		return $this->connection->table('user')
			->where('email', $email)
			->where('active', 1) // musi byt aktivni jinak ma smulu heslo si neposle
			->fetch();
	}


	public function wasPasswordSend($lastSend = null, $expire = '24 hour')
	{
		if ($lastSend === null)
			return false;

		$dt = new \DateTime();
		$dt->modify('-' . $expire);

		$lastSend = new \DateTime($lastSend);

		if ($lastSend->getTimestamp() < $dt->getTimestamp())
			return false;

		return true;
	}


	public function saveLogin($userId)
	{
		// $this->connection->table('user')
		// 	->where('id', $userId)
		// 	->update([
		// 		'last_login' => new \DateTime
		// 	]);

		$this->connection->query('UPDATE user SET last_login = ? WHERE id = ?',
			$userId,
			new \DateTime
		);
	}

	public function saveLogout($userId)
	{
		// $this->connection->table('user')
		// 	->where('id', $userId)
		// 	->update([
		// 		'last_logout' => new \DateTime
		// 	]);

		$this->connection->query('UPDATE user SET last_logout = ? WHERE id = ?',
			$userId,
			new \DateTime
		);
	}

	public function saveLog($userId, $url)
	{
		$this->connection->table('user_logger')
			->insert([
				'user_id' => $userId,
				'created' => new \DateTime,
				'url' => $url
			]);

		$this->connection->query('UPDATE user SET last_move = ? WHERE id = ?',
			$userId,
			new \DateTime
		);
		// 	$this->connection->table('user')
		// 		->where('id', $userId)
		// 		->update([
		// 			'last_move' => new \DateTime
		// 	]);
	}


}