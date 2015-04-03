<?php

namespace PrestaShop\IoC\Tests;

use PHPUnit_Framework_TestCase;

use PrestaShop_IoC_Container as Container;

use PrestaShop\IoC\Tests\Fixtures\Dummy;
use PrestaShop\IoC\Tests\Fixtures\ClassWithDep;

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

    public function test_bind_className()
    {
        $this->container->bind('dummy', 'PrestaShop\IoC\Tests\Fixtures\Dummy');

        $this->assertEquals('PrestaShop\IoC\Tests\Fixtures\Dummy', get_class(
            $this->container->make('dummy')
        ));
    }

    public function test_make_without_bind()
    {
        $this->assertEquals('PrestaShop\IoC\Tests\Fixtures\Dummy', get_class(
            $this->container->make('PrestaShop\IoC\Tests\Fixtures\Dummy')
        ));
    }

    public function test_deps_are_fetched_automagically()
    {
        $this->assertEquals('PrestaShop\IoC\Tests\Fixtures\ClassWithDep', get_class(
            $this->container->make('PrestaShop\IoC\Tests\Fixtures\ClassWithDep')
        ));
    }

    public function test_deps_are_fetched_automagically_When_dependsOnThingWithADefaultValue()
    {
        $this->assertEquals('PrestaShop\IoC\Tests\Fixtures\ClassWithDepAndDefault', get_class(
            $this->container->make('PrestaShop\IoC\Tests\Fixtures\ClassWithDepAndDefault')
        ));
    }

    /**
     * @expectedException PrestaShop_IoC_Exception
     */
    public function test_unbuildable_not_built()
    {
        $this->container->make('PrestaShop\IoC\Tests\Fixtures\UnBuildable');
    }

    /**
     * @expectedException PrestaShop_IoC_Exception
     */
    public function test_non_existing_class_not_built()
    {
        $this->container->make('PrestaShop\IoC\Tests\Fixtures\AClassThatDoesntExistAtAll');
    }

    /**
     * @expectedException PrestaShop_IoC_Exception
     */
    public function test_dependency_loop_doesnt_crash_container()
    {
        /**
         * CycleA depends on CycleB,
         * CycleB depends on CycleA
         */
        $this->container->make('PrestaShop\IoC\Tests\Fixtures\CycleA');
    }
}
