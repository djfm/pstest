<?php

use PHPUnit_Framework_TestCase;

use PrestaShop\Selenium\SeleniumServerFactory;

class SeleniumServerFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testJARFileIsFound()
    {
        $ssf = new SeleniumServerFactory();
        $this->assertInternalType('string', $ssf->getPathToServerJARFile());
    }
}
