<?php

namespace PrestaShop\TestRunner\Tests\Fixtures;

use PrestaShop\TestRunner\TestCase\TestCase;
use PrestaShop\TestRunner\Tests\Fixtures\RunnerPlugin;

class PluginTest extends TestCase
{
    public function getRunnerPlugins()
    {
        return [
            'a plugin' => new RunnerPlugin,
            'another' => new RunnerPlugin
        ];
    }

    public function setRunnerPluginData($pluginName, $pluginData)
    {

    }

    public function testSomething()
    {

    }
}
