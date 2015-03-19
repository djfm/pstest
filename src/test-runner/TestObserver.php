<?php

namespace PrestaShop\TestRunner;

use Exception;

class TestObserver
{
    private $context = array();

    private $runningStack = array();
    private $completedStack = array();

    public function setContext(array $context)
    {
        if (!empty($this->context)) {
            throw new Exception('Context was already set.');
        }

        $this->context = $context;

        return $this;
    }

    private function makeTestResult($shortName, $fullName, $startTime = null)
    {
        if (null === $startTime) {
            $startTime = microtime(true);
        }

        $result = new TestResult($shortName, $fullName, $startTime);

        return $result;
    }

    public function startTest($name, array $arguments = array(), $description = null)
    {
        if (empty($this->runningStack)) {
            $result = $this->makeTestResult($name, $name);
        } else {
            $parentResult = end($this->runningStack);
            $result = $this->makeTestResult($name, $parentResult->getFullName() . '.' . $name);
        }

        $this->runningStack[] = $result;

        return $this;
    }

    public function addException(Exception $e)
    {

    }

    public function addFile($name, $path, array $metaData = array())
    {

    }

    public function addMessage($message, $type = 'info', array $metaData = array())
    {

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

        $this->completedStack[] = array_pop($this->runningStack);
    }
}
