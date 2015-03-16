<?php

namespace PrestaShop\ConfMap\Tests;

use PHPUnit_Framework_TestCase;

use PrestaShop\ConfMap\ArrayWrapper;

class ArrayWrapperTest extends PHPUnit_Framework_TestCase
{
    public function test_splitPath_When_NoPath()
    {
        $this->assertEquals(null, ArrayWrapper::splitPath(''));
    }

    public function test_splitPath_When_OnePart()
    {
        $this->assertEquals(['hello'], ArrayWrapper::splitPath('hello'));
    }

    public function test_splitPath_When_TwoParts()
    {
        $this->assertEquals(['hello', 'world'], ArrayWrapper::splitPath('hello.world'));
    }

    public function test_get_When_NoPath()
    {
        $wrapper = new ArrayWrapper([]);

        $this->assertEquals(null, $wrapper->get('hello'));
    }

    public function test_get_When_NoPath_withFound()
    {
        $wrapper = new ArrayWrapper([]);

        $this->assertEquals([null, false], $wrapper->get('hello', null, true));
    }

    public function test_get_DefaultValue_When_NoPath()
    {
        $wrapper = new ArrayWrapper([]);

        $this->assertEquals('world', $wrapper->get('hello', 'world'));
    }

    public function test_get_Path_When_OnePart()
    {
        $wrapper = new ArrayWrapper(['a' => 42]);

        $this->assertEquals(42, $wrapper->get('a'));
    }

    public function test_get_Path_When_TwoParts()
    {
        $wrapper = new ArrayWrapper(['a' => ['b' => 43]]);

        $this->assertEquals(43, $wrapper->get('a.b'));
    }

    public function test_get_Path_When_ThreeParts()
    {
        $wrapper = new ArrayWrapper(['a' => ['b' => ['c' => 44]]]);

        $this->assertEquals(44, $wrapper->get('a.b.c'));
    }

    public function setExamples()
    {
        return [
            ['a'],
            ['a.b'],
            ['a.b.c'],
        ];
    }

    /**
     * @dataProvider setExamples
     */
    public function test_set($path)
    {
        $wrapper = new ArrayWrapper([]);
        $wrapper->set($path, 42);

        $this->assertEquals(42, $wrapper->get($path));
    }
}
