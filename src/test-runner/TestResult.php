<?php

namespace PrestaShop\TestRunner;

use Exception;

class TestResult
{
    private $shortName;
    private $fullName;
    private $startTime;

    private $events;

    public function __construct($shortName, $fullName, $startTime)
    {
        $this->shortName = $shortName;
        $this->fullName = $fullName;
        $this->startTime = $startTime;
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
}
