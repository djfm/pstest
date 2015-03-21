<?php

namespace PrestaShop\TestRunner\Tests;

use Exception;

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

    public function test_annotation_beforeClass()
    {
        $test = new SmokeTest;
        $this->assertEquals(
            array_map('strtolower', ['setupBeforeClass', 'someOtherInitialization']),
            array_map('strtolower', $test->getMethodsToCallBeforeClass())
        );
    }

    public function test_annotation_afterClass()
    {
        $test = new SmokeTest;
        $this->assertEquals(
            array_map('strtolower', ['teardownAfterClass', 'someOtherTeardown']),
            array_map('strtolower', $test->getMethodsToCallAfterClass())
        );
    }

    public function test_setupBeforeClass_tearDownAfterClass_Are_Called()
    {
        $aggregator = $this->getMockBuilder('PrestaShop\TestRunner\TestAggregator')->getMock();

        $test = $this
             ->getMockBuilder('PrestaShop\TestRunner\Tests\Fixtures\SmokeTest')
             ->setMethods(['setupBeforeClass', 'tearDownAfterClass'])
             ->getMock();

        $test->setTestAggregator($aggregator);

        $test->expects($this->once())->method('setupBeforeClass');
        $test->expects($this->once())->method('tearDownAfterClass');

        $test->run();
    }

    public function test_tearDownAfterClass_Called_EvenWhen_setupBeforeClass_Fails()
    {
        $aggregator = $this->getMockBuilder('PrestaShop\TestRunner\TestAggregator')->getMock();

        $test = $this
             ->getMockBuilder('PrestaShop\TestRunner\Tests\Fixtures\SmokeTest')
             ->setMethods(['setupBeforeClass', 'tearDownAfterClass'])
             ->getMock();

        $test->setTestAggregator($aggregator);

        $test->method('setupBeforeClass')->will($this->throwException(new Exception));

        $test->expects($this->once())->method('setupBeforeClass');
        $test->expects($this->once())->method('tearDownAfterClass');

        $test->run();
    }

    public function test_setup_and_teardown_are_called()
    {
        $aggregator = $this->getMockBuilder('PrestaShop\TestRunner\TestAggregator')->getMock();

        $test = $this
             ->getMockBuilder('PrestaShop\TestRunner\Tests\Fixtures\SmokeTest')
             ->setMethods(['setup', 'teardown'])
             ->getMock();

        $test->setTestAggregator($aggregator);

        $test->expects($this->exactly(3))->method('setup');
        $test->expects($this->exactly(3))->method('teardown');

        $test->run();
    }
}
