<?php

namespace PrestaShop\TestRunner\Tests;

use Exception;

use PHPUnit_Framework_TestCase;

use PrestaShop\TestRunner\Runner;
use PrestaShop\TestRunner\TestAggregator;
use PrestaShop\TestRunner\TestAggregatorSummarizer;

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
            array_map('strtolower', ['someOtherTeardown', 'teardownAfterClass']),
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

    public function test_tests_are_executed()
    {
        $aggregator = $this->getMockBuilder('PrestaShop\TestRunner\TestAggregator')->getMock();

        $test = $this
             ->getMockBuilder('PrestaShop\TestRunner\Tests\Fixtures\SmokeTest')
             ->setMethods(['test_Installation', 'test_ICanLoginToTheBackOffice', 'test_ICanValidateAnOrder'])
             ->getMock();

        $test->setTestAggregator($aggregator);

        $test->expects($this->exactly(1))->method('test_Installation');
        $test->expects($this->exactly(1))->method('test_ICanLoginToTheBackOffice');
        $test->expects($this->exactly(1))->method('test_ICanValidateAnOrder');

        $test->run();
    }

    public function test_tests_are_not_executed_when_setupBeforeClass_fails()
    {
        $aggregator = $this->getMockBuilder('PrestaShop\TestRunner\TestAggregator')->getMock();

        $test = $this
             ->getMockBuilder('PrestaShop\TestRunner\Tests\Fixtures\SmokeTest')
             ->setMethods(['setupBeforeClass', 'test_Installation', 'test_ICanLoginToTheBackOffice', 'test_ICanValidateAnOrder'])
             ->getMock();

        $test->method('setupBeforeClass')->will($this->throwException(new Exception));
        $test->expects($this->once())->method('setupBeforeClass');

        $test->setTestAggregator($aggregator);

        $test->expects($this->never())->method('test_Installation');
        $test->expects($this->never())->method('test_ICanLoginToTheBackOffice');
        $test->expects($this->never())->method('test_ICanValidateAnOrder');

        $test->run();
    }

    public function test_tests_are_not_executed_when_setup_fails()
    {
        $aggregator = $this->getMockBuilder('PrestaShop\TestRunner\TestAggregator')->getMock();

        $test = $this
             ->getMockBuilder('PrestaShop\TestRunner\Tests\Fixtures\SmokeTest')
             ->setMethods(['setup', 'test_Installation'])
             ->getMock();

        $test->method('setup')->will($this->throwException(new Exception));
        $test->expects($this->exactly(3))->method('setup');

        $test->setTestAggregator($aggregator);

        $test->expects($this->never())->method('test_Installation');

        $test->run();
    }

    public function test_all_ok_if_no_exception()
    {
        $aggregator = new TestAggregator;

        $test = new SmokeTest;

        $test->setTestAggregator($aggregator);

        $test->run();

        $summarizer = new TestAggregatorSummarizer;
        $summarizer->addAggregator($aggregator);

        $stats = $summarizer->getStatistics();

        $this->assertEquals(3, $stats['ok']);
        $this->assertEquals(3, $stats['details']['ok']['success']);
    }

    public function test_everyThing_skipped_When_setupBeforeClass_fails()
    {
        $aggregator = new TestAggregator;

        $test = $this
             ->getMockBuilder('PrestaShop\TestRunner\Tests\Fixtures\SmokeTest')
             ->setMethods(['setupBeforeClass'])
             ->getMock();

        $test->method('setupBeforeClass')->will($this->throwException(new Exception));

        $test->setTestAggregator($aggregator);

        $test->run();

        $summarizer = new TestAggregatorSummarizer;
        $summarizer->addAggregator($aggregator);

        $stats = $summarizer->getStatistics();

        $this->assertEquals(3, $stats['ko']);
        $this->assertEquals(3, $stats['details']['ko']['skipped']);
    }

    public function test_everyThing_fails_When_setup_fails()
    {
        $aggregator = new TestAggregator;

        $test = $this
             ->getMockBuilder('PrestaShop\TestRunner\Tests\Fixtures\SmokeTest')
             ->setMethods(['setup'])
             ->getMock();

        $test->method('setup')->will($this->throwException(new Exception));

        $test->setTestAggregator($aggregator);

        $test->run();

        $summarizer = new TestAggregatorSummarizer;
        $summarizer->addAggregator($aggregator);

        $stats = $summarizer->getStatistics();

        $this->assertEquals(3, $stats['ko']);
    }

    public function test_firstFailure_aborts_plan()
    {
        $aggregator = $this->getMockBuilder('PrestaShop\TestRunner\TestAggregator')->getMock();

        $test = $this
             ->getMockBuilder('PrestaShop\TestRunner\Tests\Fixtures\SmokeTest')
             ->setMethods(['test_Installation', 'test_ICanLoginToTheBackOffice', 'test_ICanValidateAnOrder'])
             ->getMock();

        $test->method('test_Installation')->will($this->throwException(new Exception));
        $test->expects($this->once())->method('test_Installation');

        $test->expects($this->never())->method('test_ICanLoginToTheBackOffice');
        $test->expects($this->never())->method('test_ICanValidateAnOrder');

        $test->setTestAggregator($aggregator);

        $test->run();
    }

}
