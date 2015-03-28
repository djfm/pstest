<?php

namespace PrestaShop\TestRunner;

use PrestaShop\TestRunner\Command\TestRun as TestRunCommand;

abstract class RunnerPlugin
{
    public function setup(array $options = array())
    {

    }

    public function teardown()
    {

    }

    public function getRunnerPluginData()
    {
        return [];
    }

    /**
     * Should add any options needed to the test:run command,
     * and return the list of such added options as an array of strings.
     * The options will be passed to setup.
     */
    public function addOptionsToCommand(TestRunCommand $command)
    {
        return [];
    }
}
