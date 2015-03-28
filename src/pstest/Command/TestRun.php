<?php

namespace PrestaShop\PSTest\Command;

use PrestaShop\TestRunner\Command\TestRun as BaseTestRun;

use PrestaShop\PSTest\RunnerPlugin\Selenium as SeleniumPlugin;

class TestRun extends BaseTestRun
{
    protected function configure()
    {
        parent::configure();
        $this->addRunnerPlugin(new SeleniumPlugin);
    }
}
