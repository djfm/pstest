<?php

namespace PrestaShop\PSTest\Helper;

use Exception;
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

    public function databaseExists($databaseName)
    {
        $res = $this->getPDO()->query(sprintf(
            'SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = \'%s\';',
            $databaseName
        ));

        return count($res->fetchAll()) === 1;
    }

    /**
     * Wrapper to easily build mysql commands: sets password, port, user
     * @todo: probably flaky on Windows
     */
    private function buildMySQLCommand($executable, array $arguments = array())
    {
        $parts = array(
            escapeshellarg($executable),
            '-h', escapeshellarg($this->host),
            '-u', escapeshellarg($this->user),
            '-P', escapeshellarg($this->port),
        );
        if ($this->pass)
        {
            $parts[] = '-p'.escapeshellarg($this->pass);
        }
        $parts = array_merge($parts, array_map('escapeshellarg', $arguments));
        return implode(' ', $parts);
    }

    /**
     * Like exec, but will raise an exception if the command failed.
     */
    private function exec($command)
    {
        $output = array();
        $ret = 1;
        exec($command, $output, $ret);
        if ($ret !== 0)
        {
            throw new Exception(sprintf('Unable to exec command: `%s`.', $command));
        }
        return $output;
    }

    public function dumpDatabase($databaseName, $target)
    {
        $dumpCommand = $this->buildMySQLCommand('mysqldump', array($databaseName));
        $dumpCommand .= ' > ' . escapeshellarg($target);
        $this->exec($dumpCommand);

        return $this;
    }

    public function loadDatabase($databaseName, $source)
    {
        if (!$this->databaseExists($databaseName)) {
            $this->createDatabase($databaseName);
        }

        $loadCommand = $this->buildMySQLCommand('mysql', array($databaseName));
        $loadCommand .= ' < ' . escapeshellarg($source);
        $this->exec($loadCommand);

        return $this;
    }
}
