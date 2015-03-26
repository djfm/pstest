<?php

namespace PrestaShop\TestRunner;

abstract class RunnerPlugin
{
    public function setup()
    {

    }

    public function teardown()
    {

    }

    public function getRunnerPluginData()
    {
        return [];
    }
}
