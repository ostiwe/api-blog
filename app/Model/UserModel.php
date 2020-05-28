<?php

namespace Blog\Model;

use Exception;
use R;
use RedBeanPHP\OODBBean;

class UserModel
{

	const CAN_CREATE_POST = 1 << 0;
	const CAN_DELETE_POST = 1 << 1;


	const CAN_CREATE_COMMENT = 1 << 2;
	const CAN_DELETE_COMMENT = 1 << 3;

	private $id;
	private $uid;
	private $username;
	private $email;
	private $password;
	private $mask;
	private $firstName;
	private $lastName;
	private int $age;
	private int $sex;


	private $lastError;
	private OODBBean $bean;

	public function __construct()
	{
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return mixed
	 */
	public function getUid()
	{
		return $this->uid;
	}

	/**
	 * @return mixed
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * @param mixed $username
	 *
	 * @return UserModel
	 */
	public function setUsername($username)
	{
		$this->username = $username;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * @param mixed $email
	 *
	 * @return UserModel
	 */
	public function setEmail($email)
	{
		$this->email = $email;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @param mixed $password
	 *
	 * @param bool  $needHash
	 *
	 * @return UserModel
	 */
	public function setPassword($password, bool $needHash = true)
	{
		$needHash ? $this->password = password_hash($password, PASSWORD_DEFAULT) : $this->password = $password;
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
	 * @return UserModel
	 */
	public function setMask($mask)
	{
		$this->mask = $mask;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLastError()
	{
		return $this->lastError;
	}

	/**
	 * @return OODBBean
	 */
	public function getBean(): OODBBean
	{
		return $this->bean;
	}

	/**
	 * @return mixed
	 */
	public function getFirstName()
	{
		return $this->firstName;
	}

	/**
	 * @param mixed $firstName
	 *
	 * @return UserModel
	 */
	public function setFirstName($firstName)
	{
		$this->firstName = $firstName;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getLastName()
	{
		return $this->lastName;
	}

	/**
	 * @param mixed $lastName
	 *
	 * @return UserModel
	 */
	public function setLastName($lastName)
	{
		$this->lastName = $lastName;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAge()
	{
		return $this->age;
	}

	/**
	 * @param int $age
	 *
	 * @return UserModel
	 */
	public function setAge($age)
	{
		$this->age = $age;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getSex()
	{
		return $this->sex;
	}

	/**
	 * @param int $sex
	 *
	 * @return UserModel
	 */
	public function setSex($sex)
	{
		$this->sex = $sex;
		return $this;
	}

	public function create()
	{
		R::begin();
		try {
			$uid = uuid_create();
			$user = R::dispense("user");
			$user->uid = $uid;
			$user->username = $this->username;
			$user->email = $this->email;
			$user->password = $this->password;
			$user->mask = $this->mask;
			$user->first_name = $this->firstName;
			$user->last_name = $this->lastName;
			$user->age = $this->age;
			$user->sex = $this->sex;
			$userId = R::store($user);
			R::commit();
			self::load($userId);

		} catch (Exception $exception) {
			$this->lastError = $exception->getMessage();
			R::rollback();
		}
		return $this;
	}

	/**
	 * @param int|string $identifier id or uid
	 *
	 * @return UserModel
	 * @throws Exception
	 */
	public function load($identifier)
	{
		$user = NULL;
		if (is_int((int)$identifier)) {
			$user = R::load('user', (int)$identifier);
		}
		if (is_string($identifier) && $user === NULL) {
			$user = R::findOne('user', 'uid = ?', [$identifier]);
		}
		if ($user === NULL || $user['id'] === 0) throw new Exception('User not found');

		$this->id = $user['id'];
		$this->uid = $user['uid'];
		$this->username = $user['username'];
		$this->password = $user['password'];
		$this->email = $user['email'];
		$this->bean = $user;
		$this->firstName = $user->first_name;
		$this->lastName = $user->last_name;
		$this->age = (int)$user->age;
		$this->sex = (int)$user->sex;

//            R::getWriter()->addUniqueIndex('user', ['uid']);
//            R::getWriter()->addUniqueIndex('user', ['username']);
//            R::getWriter()->addUniqueIndex('user', ['email']);

		return $this;
	}
}