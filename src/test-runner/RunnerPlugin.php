<?php

namespace PrestaShop\TestRunner;

use PrestaShop\TestRunner\Command\TestRun as TestRunCommand;

abstract class RunnerPlugin
{
    /**
     * Should return an array of PrestaShop\TestRunner\Command\CLIOption
     * that you want to pass to the CLI test runner.
     */
    public function getCLIOptions()
    {
        return [];
    }

    /**
     * Will be invoked with the list of option values
     * according to what getCommandLineOptions defines.
     * This runs ONCE per plugin, before any test plan is started.
     */
    public function setup(array $options = array())
    {

    }

    /**
     * This runs after all test plans have finished.
     * Useful to stop a background task for instance.
     */
    public function teardown()
    {

    }

    /**
     * Should return the data you want to pass to the PrestaShop\TestRunner\TestPlanInterface
     */
    public function getRunnerPluginData()
    {
        return [];
    }
}
