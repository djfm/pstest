<?php

namespace PrestaShop\PSTest\RunnerPlugin;

use PrestaShop\TestRunner\RunnerPlugin;
use PrestaShop\TestRunner\Command\TestRun as TestRunCommand;

use PrestaShop\PSTest\Shop\DefaultSettings;
use PrestaShop\PSTest\SystemSettings;
use PrestaShop\PSTest\LocalShopSourceSettings;

class ConfigReader extends RunnerPlugin
{
    protected function getConfigurationFileName()
    {
        return 'pstest.settings.json';
    }

    public function getRunnerPluginData()
    {
        $systemSettings  = new SystemSettings;
        $sourceSettings  = new LocalShopSourceSettings;
        $defaultSettings = new DefaultSettings;

        $systemSettings->loadFile($this->getConfigurationFileName());
        $sourceSettings->loadFile($this->getConfigurationFileName());
        $defaultSettings->loadFile($this->getConfigurationFileName());

        return compact('systemSettings', 'sourceSettings', 'defaultSettings');
    }
}
