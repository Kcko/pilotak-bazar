<?php

namespace App\FrontModule\Model;

use Nette,
Andweb;

use Andweb\Database\Context;
use App\FrontModule\Model\NavigationWorker;


class AdPhoto
{
	/**
	 * @var Context
	 */
	protected $connection;

	/**
	 * @var Nette\Http\Request
	 */
	protected $httpRequest;


	public function __construct(Context $connection, Nette\Http\Request $httpRequest)
	{
		$this->connection = $connection;
		$this->httpRequest = $httpRequest;
	}


	/* ------------------------ Galerie, obrazky, upload ------------------------ */
	public function getImagesByRelToken($token)
	{
		return $this->connection->table('image')
			->where('relation_token', $token)
			->order('temp_rank ASC')
			->fetchAll();
	}

	public function addImageByToken(array $data = [], $file, $token)
	{
		$file = $this->httpRequest->getFile('file');

		$editState = $this->isAdExistsYet($token);
		if ($editState) {
			$adId = $token;
		}

		$data['file_name'] = $file->getSanitizedName();
		$data['size'] = $file->getSize();
		$data['ext'] = pathinfo($data['file_name'], PATHINFO_EXTENSION);


		$rank = 1;
		if ($highestImageByToken = $this->getHighestImageByToken($token)) {
			$rank = $highestImageByToken->temp_rank + 1;
		}

		$row = $this->connection->table('image')
			->insert([
				'temp_rank' => $rank,
				'is_orphan' => $editState ? 0 : 1,
				'relation_token' => $token,
				'image_folder_id' => $data['image_folder_id'],
				'ext' => $data['ext'],
				'size' => $data['size'],
				'file_name' => $data['file_name'],
				'added' => new \DateTime
			]);

		$this->connection->table('image_lang')
			->insert([
				'image_id' => $row->id,
				'language_id' => 1,
				'alt' => ''
			]);


		$file->move(STORAGE_DIR . '/images/original/' . $row->id . '.' . $data['ext']);

		if ($editState && $adId) {
			$this->sync($adId, $token);
		}

		return $row;
	}


	public function getHighestImageByToken($token)
	{
		return $this->connection->table('image')
			->where('relation_token', $token)
			->order('temp_rank DESC')
			->limit(1)
			->fetch();
	}


	public function reorderRanks(array $ids = [], $token)
	{
		$rank = 1;
		foreach ($ids as $id) {
			$this->connection->table('image')
				->where('relation_token', $token)
				->where('id', $id)
				->update(['temp_rank' => $rank]);

			$rank = $rank + 1;
		}
	}


	public function deleteImageByToken($id, $token)
	{
		$editState = $this->isAdExistsYet($token);
		if ($editState) {
			$adId = $token;
		}

		$image = $this->connection->table('image')
			->where('relation_token', $token)
			->where('id', $id)
			->fetch();

		$image->delete();

		$images = $this->getImagesByRelToken($token);
		$ids = array_map(function ($row) {
			return $row->id;
		}, $images);

		$this->reorderRanks($ids, $token);

		//@unlink(STORAGE_DIR . '/images/original/' . $image->id . '.' . $image['ext']);
	}


	public function sync($adId, $token)
	{
		$adPhoto = $this->connection->table('ad_photo')
			->where('relation_token', $token);

		$adPhoto->delete();

		foreach ($this->getImagesByRelToken($token) as $photo) {
			$adPhoto->insert([
				'ad_id' => $adId,
				'image_id' => $photo->id,
				'relation_token' => $photo->relation_token,
				'rank' => $photo->temp_rank,
			]);
		}
	}


	public function finallySaveNewAdPhotos($adId, $token)
	{
		$adPhoto = $this->connection->table('ad_photo')
			->where('relation_token', $token);

		foreach ($this->getImagesByRelToken($token) as $photo) {
			$adPhoto->insert([
				'ad_id' => $adId,
				'image_id' => $photo->id,
				'relation_token' => $adId,
				'rank' => $photo->temp_rank,
			]);
			$photo->update([
				'is_orphan' => 0,
				'relation_token' => $adId
			]);
		}

	}


	public function isAdExistsYet($token)
	{
		return Nette\Utils\Strings::length($token) < 20;
	}


}