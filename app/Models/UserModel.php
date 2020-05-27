<?php

namespace Blog\Models;

use Exception;
use R;

class UserModel
    {

        const CAN_CREATE_POST = 1 << 0;
        const CAN_DELETE_POST = 1 << 1;


        const CAN_CREATE_COMMENT = 1 << 2;
        const CAN_DELETE_COMMENT = 1 << 3;

        private $id;
        private $username;
        private $email;
        private $password;
        private $mask;

        private $lasError;

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
        public function getLasError()
        {
            return $this->lasError;
        }

        public function create()
        {
            R::begin();
            try {
                $user = R::dispense("user");
                $user->username = $this->username;
                $user->email = $this->email;
                $user->password = $this->password;
                $userId = R::store($user);
                R::commit();
                $this->id = $userId;
            } catch (Exception $exception) {
                $this->lasError = $exception->getMessage();
                R::rollback();
            }
            return $this;
        }

        public function load()
        {
            R::begin();
            try {
                $user = R::dispense("user");
                $user->uid = uuid_create();
                $user->username = 'as';
                $user->email = '3';
                $user->password = 'd';


                $userId = R::store($user);
                R::commit();
            } catch (Exception $exception) {
                $userId = $exception->getMessage();
                R::rollback();
            }


//            R::getWriter()->addUniqueIndex('user', ['uid']);
//            R::getWriter()->addUniqueIndex('user', ['username']);
//            R::getWriter()->addUniqueIndex('user', ['email']);

            return $userId;
        }
    }