<?php

namespace App\FrontModule\Model;

use Nette,
	Andweb;

use Andweb\Database\Context;

class Contact 
{

	/**
	 * @var Context
	 */
	protected $connection;


	public function __construct(Context $connection)
	{
		$this->connection = $connection;
	}

	
	public function saveData($data)
	{
		return $this->connection->table('pp_form')
			->insert($data);
	}


	public function listCategory($exclude = [])
	{
		$selection = $this->connection->table('pp_form_category');
		if ($exclude) {
			$selection->where('id NOT ?', $exclude);
		}
		$selection->order('name');

		return $selection;
	}


	public function getCategoryById($id)
	{
		return $this->connection->table('pp_form_category')
			->where('id', $id)
			->fetch(); 
	} 


	public function getEmailsArray($emails)
	{
		$emailsArr = [];
		if ($emails = preg_split('~[,;]~', $emails)) {
			foreach ($emails as $email)	{
				$email = trim($email);
				if(Nette\Utils\Validators::isEmail($email)) {
					$emailsArr[] = $email;
				}
			}
		}

		return $emailsArr;
	}

	

}



