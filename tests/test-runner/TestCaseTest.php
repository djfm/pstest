<?php

namespace PrestaShop\TestRunner\Tests;

use PHPUnit_Framework_TestCase;

use PrestaShop\TestRunner\Runner;

use PrestaShop\TestRunner\Tests\Fixtures\SmokeTest;

class TestCaseTest extends PHPUnit_Framework_TestCase
{
    public function test_getTestsCount()
    {
        $test = new SmokeTest;

        $this->assertEquals(12, $test->getTestsCount());
    }
}
