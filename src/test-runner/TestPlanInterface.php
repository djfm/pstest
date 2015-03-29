<?php

namespace PrestaShop\TestRunner;

interface TestPlanInterface
{
    public function setTestAggregator(TestAggregator $aggregator);

    public function run();

    /**
     * Conventions are:
     * -> 0, no tests
     * -> n, with n > 0 := a statically determined number of tests
     * -> null := number of tests cannot be determined statically
     */
    public function getTestsCount();

    public function getRunnerPlugins();
    public function setRunnerPluginData($pluginName, $data);
}
