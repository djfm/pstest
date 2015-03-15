<?php

namespace PrestaShop\PSTest;

class SystemSettings
{
    private $database_host = '127.0.0.1';
    private $database_port = 3306;
    private $database_user = 'root';
    private $database_pass = '';
    private $database_name;

    private $www_path; // example: /var/www/sandbox
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
