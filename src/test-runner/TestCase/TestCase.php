<?php

namespace PrestaShop\TestRunner\TestCase;

use ReflectionClass;

use PrestaShop\TestRunner\TestPlanInterface;

class TestCase implements TestPlanInterface
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

    public static function serializeAsArray(TestPlanInterface $testPlan)
    {
        return [
            'className' => get_called_class($testPlan),
            'context' => $testPlan->getContext(),
            'filePath' => $this->getFilePath()
        ];
    }

    public static function unserializeFromArray(array $array)
    {
        $testPlan = new $array['className'];
        $testPlan->setContext($array['context'])->setFilePath($array['filePath']);

        return $testPlan;
    }
}
