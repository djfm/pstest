<?php

namespace PrestaShop\PSTest\TestCase;

use ReflectionClass;
use Exception;

use PrestaShop\FileSystem\FileSystemHelper as FS;

use PrestaShop\ConfMap\ArrayWrapper;

use PrestaShop\PSTest\Cloud\Application;
use PrestaShop\PSTest\Cloud\Customer;

abstract class CloudTest extends SeleniumEnabledTest
{
    protected $application;
    protected $customer;

    private function getPathForFile($name)
    {
        $mySelf = new ReflectionClass($this);
        $directory = dirname($mySelf->getFileName());

        $path = FS::join($directory, $name);

        if (!file_exists($path)) {
            throw new Exception(sprintf('File not found: %s.', $path));
        }

        return $path;
    }

    private function loadJSON($path)
    {
        $data = json_decode(file_get_contents($path), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception(sprintf('Cannot read JSON from file: %s.', $path));
        }

        return $data;
    }

    protected function getApplicationURL()
    {
        $data = $this->loadJSON($this->getPathForFile('environment.json'));
        if (!isset($data['applicationURL'])) {
            throw new Exception('No application URL defined.');
        }
        return $data['applicationURL'];
    }

    protected function getSecrets()
    {
        return new ArrayWrapper($this->loadJSON($this->getPathForFile('secrets.json')));
    }

    protected function getEmailReader()
    {
        $secrets = $this->getSecrets();
        $className = $secrets->get('email.provider');
        return new $className($secrets->get('email.address'), $secrets->get('email.password'));
    }

    public function setupBeforeClass()
    {
        $this->browser = $this->browserFactory->makeBrowser();
        $this->setupBrowser($this->browser);

        $this->application = new Application($this->getApplicationURL(), $this->browser);

        $emailReader = $this->getEmailReader();

        $uid = md5(microtime() . getmypid() . get_called_class());

        list($base, $suffix) = explode('@', $emailReader->getAddress());
        $email = $base . '+' . $uid . '@' . $suffix;

        $password = '123456789';

        $this->customer = new Customer($email, $password, $uid, $emailReader);
    }
}
