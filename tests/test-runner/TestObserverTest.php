<?php

namespace PrestaShop\TestRunner\Tests;

use Exception;

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

        $this->assertEquals(1, count($obs->getTestResults()));
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

        $this->assertEquals(2, count($obs->getTestResults()));
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

    public function test_ExceptionEvent_Recorded()
    {
        $obs = $this->makeObserver();
        $obs->startTest('a');
        $obs->addException(new Exception('a failed'));
        $obs->endTest('a', false, 'failure');

        $this->assertEquals('a failed', $obs->getTestResult('a')->getEvent(0)->getException()->getMessage());
    }
}
