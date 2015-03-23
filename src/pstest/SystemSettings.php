<?php

namespace PrestaShop\PSTest;

use PrestaShop\ConfMap\Configuration;

/**
 * @root system
 */
class SystemSettings extends Configuration
{
    /**
     * @conf database.host
     * @var string
     */
    private $database_host = '127.0.0.1';

    /**
     * @conf database.port
     * @var int
     */
    private $database_port = 3306;

    /**
     * @conf database.user
     * @var string
     */
    private $database_user = 'root';

    /**
     * @conf database.pass
     * @var string
     */
    private $database_pass = '';

    /**
     * @conf database.name
     * @var string
     */
    private $database_name;

    /**
     * @conf database.tables_prefix
     */
    private $database_tables_prefix = 'ps_';

    /**
     * @conf www.path
     * @var string
     */
    private $www_path; // example: /var/www/sandbox

    /**
     * @conf www.base
     * @var string
     */
    private $www_base; // example: http://localhost/sandbox


    public function getDatabaseHost()
    {
        return $this->database_host;
    }

    public function setDatabaseHost($database_host)
    {
        $this->database_host = $database_host;
        return $this;
    }

    public function getDatabasePort()
    {
        return $this->database_port;
    }

    public function getDatabaseHostAndPort()
    {
        return $this->getDatabaseHost() . ':' . $this->getDatabasePort();
    }

    public function getDatabaseTablesPrefix()
    {
        return $this->database_tables_prefix;
    }

    public function setDatabasePort($database_port)
    {
        $this->database_port = $database_port;
        return $this;
    }

    public function getDatabaseUser()
    {
        return $this->database_user;
    }

    public function setDatabaseUser($database_user)
    {
        $this->database_user = $database_user;
        return $this;
    }

    public function getDatabasePass()
    {
        return $this->database_pass;
    }

    public function setDatabasePass($database_pass)
    {
        $this->database_pass = $database_pass;
        return $this;
    }

    public function getDatabaseName()
    {
        return $this->database_name;
    }

    public function setDatabaseName($database_name)
    {
        $this->database_name = $database_name;
        return $this;
    }

    public function getWWWPath()
    {
        return $this->www_path;
    }

    public function setWWWPath($www_path)
    {
        $this->www_path = $www_path;
        return $this;
    }

    public function getWWWBase()
    {
        return $this->www_base;
    }

    public function setWWWWBase($www_base)
    {
        $this->www_base = $www_base;
        return $this;
    }
}
