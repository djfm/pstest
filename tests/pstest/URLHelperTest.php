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

    public function test_getParameter_when_multiple_parameters()
    {
        $this->assertEquals('AdminLogin', URL::getParameter('http://fa.st/PrestaShop/admin-dev/index.php?controller=AdminLogin&token=70fae6b78c9f00721bc4a1ff9fbda665', 'controller'));
    }
}
