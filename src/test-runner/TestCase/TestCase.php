<?php

namespace PrestaShop\TestRunner\TestCase;

use Exception;
use ReflectionClass;
use ReflectionMethod;

use PrestaShop\PSTest\Helper\DocCommentParser;

use PrestaShop\TestRunner\TestPlanInterface;
use PrestaShop\TestRunner\TestAggregator;

abstract class TestCase implements TestPlanInterface
{
    private $aggregator;
    private $_context = [];
    private $filePath;
    private $tests = [];
    private $skipRemainingTests = false;

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
        return [[]];
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

    public function getRunnerPlugins()
    {
        return [];
    }

    public function setRunnerPluginData($pluginName, $pluginData)
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
            $this->getMethodsByAnnotation('afterClass'),
            ['tearDownAfterClass']
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

    private function getStatusCodeFromException(Exception $e)
    {
        return 'error';
    }

    public function run()
    {
        $this->aggregator->setContext($this->getContext());
        $this->aggregator->startTest(get_called_class());

        $before = $this->getCallables($this->getMethodsToCallBeforeClass());
        $after = $this->getCallables($this->getMethodsToCallAfterClass());
        $beforeEach = $this->getCallables($this->getMethodsToCallBeforeEachTest());
        $afterEach = $this->getCallables($this->getMethodsToCallAfterEachTest());

        $allGood = true;

        $beforeOk = true;
        foreach ($before as $callable) {
            $res = $callable->run($this);
            if ($res instanceof Exception) {
                $beforeOk = false;
                $this->aggregator->addException($res);
                break;
            }
        }

        foreach ($this->tests as $test) {
            $this->aggregator->startTest($test->getName());

            if ($beforeOk && false === $this->skipRemainingTests) {
                $beforeEachOk = true;

                foreach ($beforeEach as $setup) {
                    $res = $setup->run($this);
                    if ($res instanceof Exception) {
                        $beforeEachOk = false;
                        $this->aggregator->addException($res);
                        break;
                    }
                }

                $testOk = false;
                $testException = null;
                if ($beforeEachOk) {
                    $res = $test->run($this);
                    if ($res instanceof Exception) {
                        $testException = $res;
                        $this->aggregator->addException($res);
                        $this->skipRemainingTests = true;
                    } else {
                        $testOk = true;
                    }
                }

                $afterEachOk = true;
                foreach ($afterEach as $teardown) {
                    $res = $teardown->run($this);
                    if ($res instanceof Exception) {
                        $afterEachOk = false;
                        $this->aggregator->addException($res);
                    }
                }

                if ($beforeEachOk && $testOk && $afterEachOk) {
                    $this->aggregator->endTest($test->getName(), true, 'success');
                } else {
                    $allGood = false;

                    if ($testException) {
                        // If the test itself failed, we want to distinguish between failure and error
                        $this->aggregator->endTest($test->getName(), false, $this->getStatusCodeFromException($testException));
                    } else {
                        // If something failed outside the body of the test, we always consider it an error
                        $this->aggregator->endTest($test->getName(), false, 'error');
                    }
                }
            } else {
                $this->aggregator->endTest($test->getName(), false, 'skipped');
            }
        }

        foreach ($after as $callable) {
            $res = $callable->run($this);
            if ($res instanceof Exception) {
                $this->aggregator->addException($res);
            }
        }

        $this->aggregator->endTest(get_called_class(), $allGood, $allGood ? 'success' : 'failure');
    }
}
