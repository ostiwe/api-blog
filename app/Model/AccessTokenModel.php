<?php


namespace Blog\Model;


use R;

class AccessTokenModel
{
	private UserModel $owner;
	private $created;
	private $expired;
	private $mask;

	private $accessToken;

	/**
	 * @return UserModel
	 */
	public function getOwner(): UserModel
	{
		return $this->owner;
	}

	/**
	 * @param UserModel $owner
	 *
	 * @return AccessTokenModel
	 */
	public function setOwner(UserModel $owner): AccessTokenModel
	{
		$this->owner = $owner;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCreated()
	{
		return $this->created;
	}

	/**
	 * @param mixed $created
	 *
	 * @return AccessTokenModel
	 */
	public function setCreated($created)
	{
		$this->created = $created;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getExpired()
	{
		return $this->expired;
	}

	/**
	 * @param mixed $expired
	 *
	 * @return AccessTokenModel
	 */
	public function setExpired($expired)
	{
		$this->expired = $expired;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getMask()
	{
		return $this->mask;
	}

	/**
	 * @param mixed $mask
	 *
	 * @return AccessTokenModel
	 */
	public function setMask($mask)
	{
		$this->mask = $mask;
		return $this;
	}

	public function generateToken()
	{
		try {
			$token = bin2hex(random_bytes(20));
		} catch (\Exception $e) {
			$token = bin2hex(rand(9999, PHP_INT_MAX) . 'hello,im.....' . rand(9999, PHP_INT_MAX));
		}

		return $token;
	}

	public function create()
	{
		$generatedToken = self::generateToken();

		R::begin();
		try {
			$newToken = R::dispense('token');
			$newToken->access_token = $generatedToken;
			$newToken->onwer = $this->owner->getBean();
			$newToken->expired = $this->expired;
			$newToken->created = $this->created;
			$newToken->mask = $this->mask;
			R::store($newToken);
			R::commit();

			return $generatedToken;
		} catch (\Exception $exception) {
			R::rollback();
			return false;
		}
	}

	public function verifyToken($accessToken)
	{
		$token = R::findOne('token', 'access_token = ?', [$accessToken]);
		if ($token !== NULL || $token['id'] !== 0) {
			return true;
		}
		return false;
	}

}