<?php

namespace PrestaShop\PSTest\TestCase;

use PrestaShop\TestRunner\TestCase\TestCase as BaseTestCase;
use PrestaShop\PSTest\RunnerPlugin\Selenium as SeleniumPlugin;

use PrestaShop\Selenium\SeleniumServerSettings;

use PrestaShop\Selenium\SeleniumBrowserFactory;
use PrestaShop\Selenium\SeleniumBrowserSettings;

use PrestaShop\PSTest\SystemSettings;
use PrestaShop\PSTest\LocalShopSourceSettings;
use PrestaShop\PSTest\Shop\LocalShopFactory;
use PrestaShop\PSTest\Shop\DefaultSettings;

class TestCase extends BaseTestCase
{
    private $browserFactory;
    protected $shop;

    public function getRunnerPlugins()
    {
        return [
            'selenium' => new SeleniumPlugin
        ];
    }

    public function setRunnerPluginData($pluginName, $pluginData)
    {
        if ($pluginName === 'selenium') {
            $this->setupSelenium($pluginData);
        }
    }

    public function setupSelenium(SeleniumServerSettings $serverSettings)
    {
        $browserSettings = new SeleniumBrowserSettings;
        $this->browserFactory = new SeleniumBrowserFactory($serverSettings, $browserSettings);

        register_shutdown_function(function () {
            $this->browserFactory->quitLaunchedBrowsers();
        });
    }

    protected function getConfigurationFileName()
    {
        return 'pstest.settings.json';
    }

    /**
     * @beforeClass
     */
    public function setShop()
    {
        $systemSettings  = new SystemSettings;
        $sourceSettings  = new LocalShopSourceSettings;
        $defaultSettings = new DefaultSettings;

        $systemSettings->loadFile($this->getConfigurationFileName());
        $sourceSettings->loadFile($this->getConfigurationFileName());
        $defaultSettings->loadFile($this->getConfigurationFileName());

        $shopFactory = new LocalShopFactory($this->browserFactory, $systemSettings, $sourceSettings);

        $this->shop = $shopFactory->makeShop([
            'temporary' => true
        ]);

        $this->shop->setDefaults($defaultSettings);
    }
}
