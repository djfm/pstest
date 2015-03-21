<?php

namespace PrestaShop\TestRunner\TestCase;

use ReflectionClass;
use ReflectionMethod;

use PrestaShop\TestRunner\TestPlanInterface;
use PrestaShop\TestRunner\TestAggregator;

abstract class TestCase implements TestPlanInterface
{
    private $aggregator;
    private $_context;
    private $filePath;
    private $tests = [];

    public function __construct()
    {
        $this->prepareTests();
    }

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

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function setFilePath($path)
    {
        $this->filePath = $path;

        return $this;
    }

    protected function isTestMethod(ReflectionMethod $method)
    {
        if ($method->isStatic()) {
            return false;
        }

        if (preg_match('/^test/', $method->getName())) {
            return true;
        }
    }

    private function prepareTests()
    {
        $refl = new ReflectionClass($this);

        $methods = $refl->getMethods(
            ReflectionMethod::IS_PUBLIC
        );

        foreach ($methods as $method) {
            if (!$this->isTestMethod($method)) {
                continue;
            }

            $test = (new TestMethod)->setName($method->getName());

            $this->tests[] = $test;
        }
    }

    public function getTestsCount()
    {
        return count($this->tests) * count($this->contextProvider());
    }

    public function run()
    {
        foreach ($this->tests as $test) {
            $this->aggregator->startTest($test->getName());

            $test->run($this);

            $this->aggregator->endTest($test->getName(), true, 'ok');
        }
    }
}
