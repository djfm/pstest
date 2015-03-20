<?php

namespace PrestaShop\TestRunner\TestCase;

use ReflectionClass;

use PrestaShop\TestRunner\TestPlanInterface;
use PrestaShop\TestRunner\TestAggregator;

abstract class TestCase implements TestPlanInterface
{
    private $aggregator;
    private $_context;
    private $filePath;

    public function setTestAggregator(TestAggregator $aggregator)
    {
        $this->aggregator = $aggregator;

        return $this;
    }

    public function contextProvider()
    {
        return [];
    }

    public function getContext()
    {
        return $this->_context;
    }

    public function setContext(array $context)
    {
        $this->_context = $context;

        return $this;
    }

    public function run()
    {
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function setFilePath($path)
    {
        $this->filePath = $path;

        return $this;
    }
}
