<?php

namespace PrestaShop\TestRunner\TestCase;

use Exception;
use ReflectionClass;
use ReflectionMethod;
use PHPUnit_Framework_Assert;

use PrestaShop\PSTest\Helper\DocCommentParser;
use PrestaShop\FileSystem\FileSystemHelper as FS;

use PrestaShop\TestRunner\TestPlanInterface;
use PrestaShop\TestRunner\TestAggregator;

abstract class TestCase extends PHPUnit_Framework_Assert implements TestPlanInterface
{
    private $aggregator;
    private $_context = [];
    private $filePath;
    private $tests = [];
    private $skipRemainingTests = false;
    private $filters = [];

    public function __construct(array $filters = array())
    {
        $this->filters = $filters;
        $this->prepareTests();
    }

    public function setTestAggregator(TestAggregator $aggregator)
    {
        $this->aggregator = $aggregator;

        return $this;
    }

    public function filterOut($testName)
    {
        // when no filters, everything goes
        if (empty($this->filters)) {
            return false;
        }

        $sawExcludingFilters = false;

        foreach ($this->filters as $filter) {
            if (preg_match('/^context:/i', $filter)) {
                continue;
            }

            $sawExcludingFilters = true;

            $expFilter = '/' . $filter . '/';
            if (@preg_match($expFilter, null)) {
                // if the passed filter can be interpreted as a regexp, use it as such
                if (preg_match($expFilter, $testName)) {
                    return false;
                }
            } else {
                // otherwise, just do a string comparison
                if (strpos($testName, $filter) !== false) {
                    return false;
                }
            }

        }

        // no filter matched, so skip the test (except if matching filters were non excluding for tests, e.g. context filters)
        return $sawExcludingFilters;
    }

    public function contextProvider()
    {
        return [[]];
    }

    public function getContext()
    {
        return $this->_context;
    }

    public function context($key)
    {
        if (array_key_exists($key, $this->getContext())) {
            return $this->getContext()[$key];
        } else {
            throw new Exception(
                sprintf(
                    'There is nothing with key `%s` in the current context.',
                    $key
                )
            );
        }
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

            if ($this->filterOut(get_called_class() . '::' . $method->getName())) {
                continue;
            }

            $test = (new TestMethod)->setName($method->getName());

            $this->tests[] = $test;
        }
    }

    public function getTestsCount()
    {
        return count($this->tests);
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

    public function getPackage()
    {
        return 'Unknown';
    }

    public function run()
    {
        $this->aggregator->setTestSuite(get_called_class());
        $this->aggregator->setPackage($this->getPackage());
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

    public function aTestIsRunning()
    {
        return $this->aggregator->getCurrentTest() ? true : false;
    }

    public function getCurrentTest()
    {
        $test = $this->aggregator->getCurrentTest();

        if (!$test) {
            throw new Exception('There is no currently running test!');
        }

        return $test;
    }

    public function prepareFileStorage($name)
    {
        $baseDir = 'test-artefacts';
        $classPath = explode('\\', get_called_class());

        $contextDirParts = [];
        $context = $this->getContext();
        ksort($context);
        foreach ($context as $key => $value) {
            if (is_scalar($value)) {
                $contextDirParts[] = "($key $value)";
            }
        }

        $testDir = $this->getCurrentTest()->getShortName();

        $tail = [$testDir, $name];

        if (!empty($contextDirParts)) {
            array_unshift($tail, implode(' ', $contextDirParts));
        }

        $pathParts = array_merge([$baseDir], $classPath, $tail);

        $path = FS::join($pathParts);

        $dir = dirname($path);

        if (!is_dir($dir)) {
            if (!@mkdir($dir, 0777, true)) {
                throw new Exception(sprintf(
                    'Could not create directory `%s`.'
                , $dir));
            }
        }

        return $path;
    }

    public function addFileArtefact($path, array $metaData = array())
    {
        $this->aggregator->addFile(basename($path), realpath($path), $metaData);
        return $this;
    }
}
