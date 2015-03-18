<?php

namespace PrestaShop\PSTest\Tests;

use Exception;

use PHPUnit_Framework_TestCase;

use PrestaShop\PSTest\Helper\URLHelper as URL;

class URLHelperTest extends PHPUnit_Framework_TestCase
{
    public function test_getParameter()
    {
        $this->assertEquals('world', URL::getParameter('http://example.com/greet?hello=world', 'hello'));
    }
}
