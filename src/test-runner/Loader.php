<?php

namespace PrestaShop\TestRunner;

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

    }
}
