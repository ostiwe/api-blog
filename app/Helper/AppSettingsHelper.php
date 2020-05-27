<?php


namespace Blog\Helper;


class AppSettingsHelper
    {
        private $dbHost;
        private $dbPort;
        private $dbName;
        private $dbUser;
        private $dbPassword;

    /**
     * @return mixed
     */
        public function getDbHost()
        {
            return $this->dbHost;
        }

    /**
     * @param mixed $dbHost
     *
     * @return AppSettingsHelper
     */
        public function setDbHost($dbHost)
        {
            $this->dbHost = $dbHost;
            return $this;
        }

    /**
     * @return mixed
     */
        public function getDbPort()
        {
            return $this->dbPort;
        }

    /**
     * @param mixed $dbPort
     *
     * @return AppSettingsHelper
     */
        public function setDbPort($dbPort)
        {
            $this->dbPort = $dbPort;
            return $this;
        }

    /**
     * @return mixed
     */
        public function getDbName()
        {
            return $this->dbName;
        }

    /**
     * @param mixed $dbName
     *
     * @return AppSettingsHelper
     */
        public function setDbName($dbName)
        {
            $this->dbName = $dbName;
            return $this;
        }

    /**
     * @return mixed
     */
        public function getDbUser()
        {
            return $this->dbUser;
        }

    /**
     * @param mixed $dbUser
     *
     * @return AppSettingsHelper
     */
        public function setDbUser($dbUser)
        {
            $this->dbUser = $dbUser;
            return $this;
        }

    /**
     * @return mixed
     */
        public function getDbPassword()
        {
            return $this->dbPassword;
        }

    /**
     * @param mixed $dbPassword
     *
     * @return AppSettingsHelper
     */
        public function setDbPassword($dbPassword)
        {
            $this->dbPassword = $dbPassword;
            return $this;
        }


    }