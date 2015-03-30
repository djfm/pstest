<?php

namespace PrestaShop\TestRunner;

use Exception;

class TestResult
{
    private $shortName;
    private $fullName;
    private $startTime;

    private $arguments = [];
    private $description;

    private $events;

    private $status;

    private $depth;

    private $children = [];

    private $testSuite = '';
    private $package = '';

    public function __construct($shortName, $fullName, $startTime, $depth)
    {
        $this->shortName = $shortName;
        $this->fullName = $fullName;
        $this->startTime = $startTime;
        $this->depth = $depth;

        $this->status = new TestStatus(false, 'unknown');
    }

    public function setTestSuite($name)
    {
        $this->testSuite = $name;
        return $this;
    }

    public function getTestSuite()
    {
        return $this->testSuite;
    }

    public function setPackage($name)
    {
        $this->package = $name;
        return $this;
    }

    public function getPackage()
    {
        return $this->package;
    }

    public function getShortName()
    {
        return $this->shortName;
    }

    public function setShortName($shortName)
    {
        $this->shortName = $shortName;
        return $this;
    }

    public function getFullName()
    {
        return $this->fullName;
    }

    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
        return $this;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function addEvent(TestEvent $event)
    {
        $this->events[] = $event;
        return $this;
    }

    public function getEvents()
    {
        return $this->events;
    }

    public function getEvent($n)
    {
        if (isset($this->events[$n])) {
            return $this->events[$n];
        }

        throw new Exception(
            sprintf(
                'No event recorded at position `%d` for test `%s`.',
                $n,
                $this->fullName
            )
        );
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus(TestStatus $status)
    {
        $this->status = $status;
        return $this;
    }

    public function getDepth()
    {
        return $this->depth;
    }

    public function addChild(TestResult $result)
    {
        $this->children[] = $result;
        return $this;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function hasChildren()
    {
        return !empty($this->children);
    }

    public function getTotalTime()
    {
        if (empty($this->getEvents())) {
            return 0;
        }
        return end($this->getEvents())->getEventTime() - $this->getStartTime();
    }

    public function getBaseName()
    {
        return rtrim(substr($this->getFullName(), 0, strlen($this->getFullName()) - strlen($this->getShortName())), ':');
    }
}
