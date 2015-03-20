<?php

namespace PrestaShop\TestRunner\Tests;

use PHPUnit_Framework_TestCase;

use PrestaShop\TestRunner\Loader;

class LoaderTest extends PHPUnit_Framework_TestCase
{
    private function getFixturePath($name)
    {
        return implode(DIRECTORY_SEPARATOR, [__DIR__, 'fixtures', $name]);
    }

    public function test_Number_Of_Plans()
    {
        $loader = new Loader();

        $loader->loadFile(
            $this->getFixturePath('SmokeTest.php')
        );

        $this->assertEquals(4, count($loader->getTestPlans()));
    }
}
