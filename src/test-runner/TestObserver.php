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

    private function makeTestResult($shortName, $fullName)
    {
        $result = new TestResult($shortName, $fullName, microtime(true));

        return $result;
    }

    private function addEventToCurrentTestResult()
    {
        if (empty($this->runningStack)) {
            throw new Exception('There is no running test, cannot add event.');
        }

        $testResult = end($this->runningStack);

        $event = new TestEvent($testResult, microtime(true));

        $testResult->addEvent($event);

        return $event;
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
            $result = $this->makeTestResult($name, $parentResult->getFullName() . '.' . $name);
        }

        $this->runningStack[] = $result;

        return $this;
    }

    public function addException(Exception $e)
    {
        $this->addEventToCurrentTestResult()->setException($e);

        return $this;
    }

    public function addFile($name, $path, array $metaData = array())
    {
        $file = new FileArtefact($name, $path);

        $this->addEventToCurrentTestResult()
             ->setMetaData($metaData)
             ->setFile($file)
        ;

        return $this;
    }

    public function addMessage($message, $type = 'info', array $metaData = array())
    {
        $message = new TestMessage($message, $type);

        $this->addEventToCurrentTestResult()
             ->setMetaData($metaData)
             ->setMessage($message);

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
