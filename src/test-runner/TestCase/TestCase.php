<?php

namespace PrestaShop\TestRunner\TestCase;

use ReflectionClass;
use ReflectionMethod;

use PrestaShop\PSTest\Helper\DocCommentParser;

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

    public function setupBeforeClass()
    {

    }

    public function setup()
    {

    }

    public function teardown()
    {

    }

    public function tearDownAfterClass()
    {

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

        $methods = $refl->getMethods();

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

    private function getMethodsByAnnotation($annotationName)
    {
        $methods = [];

        $refl = new ReflectionClass($this);
        foreach ($refl->getMethods() as $method) {
            $comment = new DocCommentParser($method->getDocComment());
            if ($comment->hasOption($annotationName)) {
                $methods[] = $method->getName();
            }
        }

        return $methods;
    }

    final public function getMethodsToCallBeforeClass()
    {
        return array_merge(
            ['setupBeforeClass'],
            $this->getMethodsByAnnotation('beforeClass')
        );
    }

    final public function getMethodsToCallAfterClass()
    {
        return array_merge(
            ['tearDownAfterClass'],
            $this->getMethodsByAnnotation('afterClass')
        );
    }

    final public function getMethodsToCallBeforeEachTest()
    {
        return ['setup'];
    }

    final public function getMethodsToCallAfterEachTest()
    {
        return ['teardown'];
    }

    /**
     * Returns instances of TestMethod for all methods whose name
     * specified in $candidates is callable by this class.
     */
    private function getCallables(array $candidates)
    {
        $callables = [];

        foreach ($candidates as $candidate) {
            if (is_callable([$this, $candidate])) {
                $callables[] = (new TestMethod)->setName($candidate);
            }
        }

        return $callables;
    }

    public function run()
    {
        $before = $this->getCallables($this->getMethodsToCallBeforeClass());
        $after = $this->getCallables($this->getMethodsToCallAfterClass());
        $beforeEach = $this->getCallables($this->getMethodsToCallBeforeEachTest());
        $afterEach = $this->getCallables($this->getMethodsToCallAfterEachTest());

        foreach ($before as $callable) {
            $callable->run($this);
        }

        foreach ($this->tests as $test) {
            $this->aggregator->startTest($test->getName());

            foreach ($beforeEach as $setup) {
                $setup->run($this);
            }

            $test->run($this);

            foreach ($afterEach as $teardown) {
                $teardown->run($this);
            }

            $this->aggregator->endTest($test->getName(), true, 'ok');
        }


        foreach ($after as $callable) {
            $callable->run($this);
        }
    }
}
