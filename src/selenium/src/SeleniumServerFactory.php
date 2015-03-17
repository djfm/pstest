<?php

namespace PrestaShop\Selenium;

use Exception;

use PrestaShop\Proc\Proc;

class SeleniumServerFactory
{
    private $start_port = 4444;
    private $end_port = 4500;
    private $host = '127.0.0.1';

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
            '-port', $port,
            '-host', escapeshellarg($this->host)
        ]);
    }

    private function _makeServer($port)
    {
        $command = $this->getStartCommand($port);
        $proc = new Proc($command);

        $proc->disableSTDOUT()->disableSTDERR();

        $proc->start();

        $url = 'http://' . $this->host . ':' . $port . '/wd/hub';

        $serverSettings = new SeleniumServerSettings();
        $serverSettings->setURL($url);

        $server = new SeleniumServer($serverSettings, $proc);

        return $server;
    }

    public function makeServer()
    {
        for ($port = $this->start_port; $port < $this->end_port; ++$port) {

            // fast test of port before starting selenium
            $conn = @fsockopen($this->host, $port);
            if (is_resource($conn)) {
                fclose($conn);
                continue;
            }

            $server = $this->_makeServer($port);
            if ($server->isRunning()) {
                return $server;
            }
        }

        throw new Exception('Could not start local selenium server.');
    }
}
