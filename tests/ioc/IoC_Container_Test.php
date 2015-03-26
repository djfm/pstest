<?php

namespace PrestaShop\IoC\Tests;

use PHPUnit_Framework_TestCase;

use PrestaShop_IoC_Container as Container;

use PrestaShop\IoC\Tests\Fixtures\Dummy;

class PrestaShop_IoC_Container_Test extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->container = new Container;
    }

    public function test_bind_by_closure()
    {
        $this->container->bind('foo', function () {
            return 'FOO';
        });

        $this->assertEquals('FOO', $this->container->make('foo'));
    }

    public function test_bind_by_closure_instance_not_shared_by_default()
    {
        $this->container->bind('different', function () {
            return new Dummy;
        });

        $first = $this->container->make('different');
        $second = $this->container->make('different');

        $this->assertNotSame($first, $second);
    }

    public function test_bind_by_closure_instance_shared_if_explicitely_required()
    {
        $this->container->bind('same', function () {
            return new Dummy;
        }, true);

        $first = $this->container->make('same');
        $second = $this->container->make('same');

        $this->assertSame($first, $second);
    }
}
