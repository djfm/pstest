<?php

namespace PrestaShop\PSTest\TestCase;

use PrestaShop\TestRunner\TestCase\TestCase;
use PrestaShop\PSTest\RunnerPlugin\Selenium as SeleniumPlugin;
use PrestaShop\Selenium\SeleniumServerSettings;
use PrestaShop\Selenium\SeleniumBrowserFactory;
use PrestaShop\Selenium\SeleniumBrowserSettings;

abstract class SeleniumEnabledTest extends TestCase
{
    protected $browserFactory;
    protected $recordScreenshots = false;
    protected $browser;
    protected $headless = false;

    public function getRunnerPlugins()
    {
        return [
            'selenium' => new SeleniumPlugin,
        ];
    }

    public function setRunnerPluginData($pluginName, $pluginData)
    {
        if ($pluginName === 'selenium') {
            $this->setupSelenium($pluginData['serverSettings'], $pluginData['recordScreenshots'], $pluginData['headless']);
        }
    }

    public function setupSelenium(SeleniumServerSettings $serverSettings, $recordScreenshots, $headless)
    {
        $browserSettings = new SeleniumBrowserSettings;
        $this->browserFactory = new SeleniumBrowserFactory($serverSettings, $browserSettings);

        $this->headless = $headless;

        $this->recordScreenshots = $recordScreenshots;

        register_shutdown_function(function () {
            $this->browserFactory->quitLaunchedBrowsers();
        });
    }
}
