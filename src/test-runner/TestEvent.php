<?php

namespace PrestaShop\TestRunner;

use Exception;

class TestEvent
{
    private $testResult;
    private $eventTime;

    private $metaData = [];

    private $exception;
    private $file;

    private $message;

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

    public function getFile()
    {
        return $this->file;
    }

    public function setFile(FileArtefact $file)
    {
        $this->file = $file;
        return $this;
    }

    public function hasException()
    {
        return $this->exception instanceof Exception;
    }

    public function hasFile()
    {
        return $this->file instanceof FileArtefact;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage(TestMessage $message)
    {
        $this->message = $message;
        return $this;
    }

    public function hasMessage()
    {
        return $this->message instanceof TestMessage;
    }
}
