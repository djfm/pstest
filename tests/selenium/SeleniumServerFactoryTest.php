<?php

namespace PrestaShop\Selenium\Tests;

use PHPUnit_Framework_TestCase;

use PrestaShop\Selenium\SeleniumServerFactory;
use PrestaShop\Selenium\SeleniumServer;

use PrestaShop\Proc\ExecutableHelper;

class SeleniumServerFactoryTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        if (!ExecutableHelper::inPath('java')) {
            $this->markTestIncomplete('Java is not installed, skipping test.');
        }
    }

    public function test_JARFileIsFound()
    {
        $ssf = new SeleniumServerFactory();
        $this->assertInternalType('string', $ssf->getPathToServerJARFile());
    }

    public function test_startCommandIsGenerated()
    {
        $ssf = new SeleniumServerFactory();
        $this->assertEquals(1, preg_match('/\.jar\b.*?\-port/', $ssf->getStartCommand(4444)));
    }

    public function test_makeServer()
    {
        $ssf = new SeleniumServerFactory();
        return $ssf->makeServer();
    }

    /**
     * @depends test_makeServer
     */
    public function test_stopServer(SeleniumServer $server)
    {
        $server->shutDown();
        sleep(1);
        $this->assertFalse($server->serverResponds(1));
        $this->assertFalse($server->isRunning());
    }

    public function test_port_automatically_chosen()
    {
        $ssf = new SeleniumServerFactory();
        $a = $ssf->makeServer();
        $b = $ssf->makeServer();

        $this->assertTrue($a->isRunning());
        $this->assertTrue($b->isRunning());

        $this->assertFalse($a->getSettings()->getURL() === $b->getSettings()->getURL());

        $a->shutDown();
        $b->shutDown();
    }
}
