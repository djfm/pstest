<?php

namespace PrestaShop\PSTest\TestCase;

use PrestaShop\TestRunner\TestCase\TestCase as BaseTestCase;
use PrestaShop\PSTest\RunnerPlugin\Selenium as SeleniumPlugin;
use PrestaShop\PSTest\RunnerPlugin\ConfigReader as ConfigReaderPlugin;

use PrestaShop\Selenium\SeleniumServerSettings;
use PrestaShop\PSTest\SystemSettings;
use PrestaShop\PSTest\LocalShopSourceSettings;

use PrestaShop\Selenium\SeleniumBrowserFactory;
use PrestaShop\Selenium\SeleniumBrowserSettings;

use PrestaShop\PSTest\Shop\LocalShopFactory;
use PrestaShop\PSTest\Shop\DefaultSettings;

abstract class TestCase extends BaseTestCase
{
    private $browserFactory;
    protected $shop;

    private $shopIsTemporary = true;

    private $systemSettings;
    private $sourceSettings;
    private $defaultSettings;

    public function getRunnerPlugins()
    {
        return [
            'selenium' => new SeleniumPlugin,
            'config' => new ConfigReaderPlugin
        ];
    }

    public function setRunnerPluginData($pluginName, $pluginData)
    {
        if ($pluginName === 'selenium') {
            $this->setupSelenium($pluginData);
        } else if ($pluginName === 'config') {
            $this->systemSettings  = $pluginData['systemSettings'];
            $this->sourceSettings  = $pluginData['sourceSettings'];
            $this->defaultSettings = $pluginData['defaultSettings'];
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

    /**
     * @beforeClass
     */
    public function setShop()
    {
        $shopFactory = new LocalShopFactory($this->browserFactory, $this->systemSettings, $this->sourceSettings);

        $this->shop = $shopFactory->makeShop([
            'temporary' => $this->shopIsTemporary
        ]);

        $this->shop->setDefaults($this->defaultSettings);
    }

    public function tearDownAfterClass()
    {
        if ($this->shopIsTemporary) {
            $this->shop->get('database')->drop();
            $this->shop->get('files')->removeAll();
        }
    }
}
