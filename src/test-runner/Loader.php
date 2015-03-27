<?php

namespace PrestaShop\TestRunner;

use ReflectionClass;

use PrestaShop\TestRunner\TestCase\TestCaseLoader;

class Loader
{
    private $loaders = [];
    private $testPlans = [];


    public function __construct()
    {
        $this->registerLoader(new TestCaseLoader);
    }

    public function registerLoader(LoaderInterface $loader)
    {
        $this->loaders[] = $loader;

        return $this;
    }

    public function loadFile($path)
    {
        $classesInFile = ClassDiscoverer::getDeclaredClasses($path);

        $classesInFile = array_filter($classesInFile, function ($className) {
            return !(new ReflectionClass($className))->isAbstract();
        });

        foreach ($this->loaders as $loader) {
            $plans = $loader->loadTestPlansFromFile($path, $classesInFile);
            if (!empty($plans)) {
                $this->testPlans = array_merge($this->testPlans, $plans);
                break;
            }
        }

        return $this;
    }

    public function getTestPlans()
    {
        return $this->testPlans;
    }
}
