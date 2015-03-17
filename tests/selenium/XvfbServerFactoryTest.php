<?php

namespace PrestaShop\Selenium\Tests;

use PrestaShop\Proc\ExecutableHelper;

use PHPUnit_Framework_TestCase;

use PrestaShop\Selenium\Xvfb\XvfbServerFactory;

class XvfbServerFactoryTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        if (!ExecutableHelper::inPath('Xvfb')) {
            $this->markTestIncomplete('Xvfb is not installed, skipping test.');
        }
    }

    public function test_factory_knows_Xvfb_is_here()
    {
        $xf = new XvfbServerFactory;
        $this->assertTrue($xf->hasXvfbProgram());
    }

    public function test_makeServer()
    {
        $xf = new XvfbServerFactory;
        $server = $xf->makeServer();
        $server->shutDown();
    }
}
