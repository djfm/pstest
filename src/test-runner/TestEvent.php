<?php

namespace PrestaShop\TestRunner;

use Exception;

class TestEvent
{
    private $testResult;
    private $eventTime;

    private $metaData = [];

    private $exception;


    public function __construct(TestResult $testResult, $eventTime)
    {
        $this->testResult = $testResult;
        $this->eventTime = $eventTime;
    }

    public function getMetaData()
    {
        return $this->metaData;
    }

    public function setMetaData($metaData)
    {
        $this->metaData = $metaData;
        return $this;
    }

    public function getException()
    {
        return $this->exception;
    }

    public function setException($exception)
    {
        $this->exception = $exception;
        return $this;
    }
}
