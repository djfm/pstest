<?php

namespace PrestaShop\PSTest\RunnerPlugin;

use Symfony\Component\Console\Input\InputOption;
use PrestaShop\TestRunner\Command\CLIOption;

use PrestaShop\TestRunner\RunnerPlugin;

class PrestaShopTest extends RunnerPlugin
{
    private $cliArgs = [];

    public function setup(array $cliArgs = array())
    {
        $this->cliArgs = $cliArgs;
    }

    public function getCLIOptions()
    {
        return [
            new CLIOption('no-cleanup', null, InputOption::VALUE_NONE, 'Do not clean temporary shops after tests')
        ];
    }

    public function getRunnerPluginData()
    {
        return $this->cliArgs;
    }
}
