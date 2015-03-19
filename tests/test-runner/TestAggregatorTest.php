<?php

namespace PrestaShop\TestRunner\Tests;

use Exception;

use PHPUnit_Framework_TestCase;

use PrestaShop\TestRunner\TestAggregator;

class TestAggregatorTest extends PHPUnit_Framework_TestCase
{
    private function makeObserver()
    {
        return new TestAggregator();
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

        $this->assertTrue($obs->getTestResult('a')->getEvent(0)->hasException());
        $this->assertEquals('a failed', $obs->getTestResult('a')->getEvent(0)->getException()->getMessage());
    }

    public function test_FileEvent_Recorded()
    {
        $obs = $this->makeObserver();
        $obs->startTest('a');
        $obs->addFile(
            'some/file.txt',
            __DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'dummy.txt',
            ['role' => 'debug']
        );
        $obs->endTest('a', true, 'success');

        $event = $obs->getTestResult('a')->getEvent(0);

        $this->assertTrue($event->hasFile());

        $file = $event->getFile();

        $this->assertEquals("dummy file!\n", $file->getContents());
        $this->assertEquals(['role' => 'debug'], $event->getMetaData());
    }

    public function test_MessageEvent_Recorded()
    {
        $obs = $this->makeObserver();
        $obs->startTest('a');
        $obs->addMessage('hello', 'debug', ['env' => 42]);
        $obs->endTest('a', false, 'failure');

        $event = $obs->getTestResult('a')->getEvent(0);

        $this->assertTrue($event->hasMessage());
        $this->assertEquals('[debug] hello', (string)$event->getMessage());
        $this->assertEquals(['env' => 42], $event->getMetaData());
    }
}
