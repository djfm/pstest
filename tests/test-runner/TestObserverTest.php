<?php

namespace PrestaShop\TestRunner\Tests;

use PHPUnit_Framework_TestCase;

use PrestaShop\TestRunner\TestObserver;

class TestObserverTest extends PHPUnit_Framework_TestCase
{
    private function makeObserver()
    {
        return new TestObserver();
    }

    public function test_properOrderOfEvents_When_NoNesting()
    {
        $obs = $this->makeObserver();
        $obs->startTest('a');
        $obs->endTest('a', true, 'success');
    }

    /**
     * @expectedException Exception
     */
    public function test_properOrderOfEvents_When_NoNesting_WrongOrder()
    {
        $obs = $this->makeObserver();
        $obs->startTest('a');
        $obs->endTest('b', true, 'success');
    }

    public function test_properOrderOfEvents_When_NestingTests()
    {
        $obs = $this->makeObserver();
        $obs->startTest('a');
        $obs->startTest('b');
        $obs->endTest('b', true, 'success');
        $obs->endTest('a', true, 'success');
    }

    /**
     * @expectedException Exception
     */
    public function test_properOrderOfEvents_When_NestingTests_WrongOrder()
    {
        $obs = $this->makeObserver();
        $obs->startTest('a');
        $obs->startTest('b');
        $obs->endTest('a', true, 'success');
        $obs->endTest('b', true, 'success');
    }
}
