<?php

namespace PrestaShop\Selenium;

class SeleniumServerFactory
{
    public function __construct()
    {

    }

    public function getPathToJARFiles()
    {
        return implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'jar']);
    }

    public function getPathToServerJARFile()
    {
        $dir = $this->getPathToJARFiles();

        foreach (scandir($dir) as $entry) {
            if (preg_match('/^selenium-server-standalone-\d+(?:\.\d+)*\.jar$/', $entry)) {
                return $dir . DIRECTORY_SEPARATOR . $entry;
            }
        }

        return null;
    }

    public function getStartCommand($port)
    {
        return implode(' ', [
            'java',
            '-jar', escapeshellcmd($this->getPathToServerJARFile()),
            '-port', $port
        ]);
    }

    public function makeServer()
    {
        $port = 4444;

        $command = $this->getStartCommand($port);
    }
}
