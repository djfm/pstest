<?php

namespace PrestaShop\TestRunner\Tests;

use PHPUnit_Framework_TestCase;

use PrestaShop\TestRunner\ClassDiscoverer;

class ClassDiscovererTest extends PHPUnit_Framework_TestCase
{
    public function test_classes_are_discovered()
    {
        // Doing it twice because an implementation might easily get it wrong
        // and fail the second time we ask the classes for a given file,
        // because the file will already have been loaded once.
        for ($i = 0; $i < 2; ++$i) {
            $this->assertEquals([
                'Example\SomeNamespace\A',
                'Example\SomeNamespace\B'
            ], ClassDiscoverer::getDeclaredClasses(
                __DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'ForDiscovery.php'
                )
            );
        }
    }
}
