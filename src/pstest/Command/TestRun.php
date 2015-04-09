<?php

namespace PrestaShop\PSTest\Command;

use PrestaShop\TestRunner\Command\TestRun as BaseTestRun;

use PrestaShop\PSTest\RunnerPlugin\Selenium as SeleniumPlugin;
use PrestaShop\PSTest\RunnerPlugin\PrestaShopTest as PrestaShopTestPlugin;

class TestRun extends BaseTestRun
{
    protected function configure()
    {
        parent::configure();
        $this->addRunnerPlugin(new SeleniumPlugin);
        $this->addRunnerPlugin(new PrestaShopTestPlugin);
    }
}
