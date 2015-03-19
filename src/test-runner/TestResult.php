<?php

namespace PrestaShop\TestRunner;

class TestResult
{
    private $shortName;
    private $fullName;
    private $startTime;

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
}
