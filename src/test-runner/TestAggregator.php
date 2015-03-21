<?php

namespace PrestaShop\TestRunner;

use Exception;

class TestAggregator
{
    /**
     * For context data shared by all tests
     * that this aggregator will see.
     */
    private $context = array();

    /**
     * Array of `TestResult`.
     *
     * Tests that have been started
     * and are not yet finished.
     */
    private $runningStack = array();

    /**
     * Array of `TestResult`.
     *
     * Completed tests, irrespective of status.
     */
    private $completedStack = array();

    private $eventListeners = [];

    public function setContext(array $context)
    {
        if (!empty($this->context)) {
            throw new Exception('Context was already set.');
        }

        $this->context = $context;

        return $this;
    }

    public function getContext()
    {
        return $this->context;
    }

    private function makeTestResult($shortName, $fullName)
    {
        $result = new TestResult($shortName, $fullName, microtime(true), count($this->runningStack));

        return $result;
    }

    private function addEventToCurrentTestResult()
    {
        if (empty($this->runningStack)) {
            throw new Exception('There is no running test, cannot add event.');
        }

        $testResult = end($this->runningStack);

        return $this->addEventToTestResult($testResult);
    }

    private function addEventToTestResult(TestResult $testResult)
    {
        $event = new TestEvent($testResult, microtime(true));

        $testResult->addEvent($event);

        return $event;
    }

    /**
     * Add an event listener to be called after any event is added to a test result, including
     * test start and test end.
     *
     * @param callable $listener A callable that takes a TestEvent and an array as parameters.
     */
    public function addEventListener(callable $listener)
    {
        $this->eventListeners[] = $listener;

        return $this;
    }

    private function onEvent(TestEvent $event)
    {
        foreach ($this->eventListeners as $listener) {
            $listener($event, $this->getContext());
        }

        return $this;
    }

    public function startTest($name, array $arguments = array(), $description = '')
    {
        if (empty($this->runningStack)) {
            $result = $this->makeTestResult($name, $name)
                           ->setArguments($arguments)
                           ->setDescription($description)
            ;
        } else {
            $parentResult = end($this->runningStack);
            $result = $this->makeTestResult($name, $parentResult->getFullName() . '::' . $name);
            $parentResult->addChild($result);
        }

        $this->runningStack[] = $result;

        $this->onEvent(
            $this->addEventToCurrentTestResult()->setIsStart()
        );

        return $this;
    }

    public function addException(Exception $e)
    {
        $this->onEvent(
            $this->addEventToCurrentTestResult()->setException($e)
        );

        return $this;
    }

    public function addFile($name, $path, array $metaData = array())
    {
        $file = new FileArtefact($name, $path);

        $this->onEvent(
            $this->addEventToCurrentTestResult()
                 ->setMetaData($metaData)
                 ->setFile($file)
        );

        return $this;
    }

    public function addMessage($message, $type = 'info', array $metaData = array())
    {
        $message = new TestMessage($message, $type);

        $this->onEvent(
            $this->addEventToCurrentTestResult()
                 ->setMetaData($metaData)
                 ->setMessage($message)
        );

        return $this;
    }

    public function endTest($name, $success, $status)
    {
        if (empty($this->runningStack)) {
            throw new Exception('There doesn\'t seem to be a test to end.');
        }

        $currentTest = end($this->runningStack);

        if ($name !== $currentTest->getShortName()) {
            throw new Exception(
                sprintf(
                    'Trying to end the `%1$s` test, but current test is actually `%2$s`.',
                    $name,
                    $currentTest->getShortName()
                )
            );
        }

        $result = array_pop($this->runningStack);

        $testStatus = new TestStatus($success, $status);

        $result->setStatus($testStatus);

        $this->completedStack[$result->getFullName()] = $result;

        $this->onEvent(
            $this->addEventToTestResult($result)->setIsEnd()
        );

        return $this;
    }

    public function getTestResults()
    {
        return $this->completedStack;
    }

    public function getTestResult($fullName)
    {
        if (isset($this->completedStack[$fullName])) {
            return $this->completedStack[$fullName];
        }

        throw new Exception(
            sprintf(
                'There is no result for test `%s`.',
                $fullName
            )
        );
    }
}
