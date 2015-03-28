<?php

namespace PrestaShop\PSTest\Helper;

use PDO;

class MySQL
{
    private $host;
    private $port;
    private $user;
    private $pass;

    public function __construct($host, $port, $user, $pass)
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->pass = $pass;
    }

    private function getPDO()
    {
        $h = new PDO("mysql:host={$this->host};port={$this->port}", $this->user, $this->pass);

        return $h;
    }

    public function createDatabase($databaseName)
    {
        $this->getPDO()->exec(sprintf('CREATE DATABASE `%s`;', $databaseName));

        return $this;
    }

    public function dropDatabase($databaseName)
    {
        $this->getPDO()->exec(sprintf('DROP DATABASE `%s`;', $databaseName));

        return $this;
    }
}
