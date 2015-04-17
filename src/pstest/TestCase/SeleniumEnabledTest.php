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

    protected function setupBrowser($browser)
    {
        if($this->headless) {
            // When running headlessly we might have a too small window
            // because there is probably no window manager running.
            // So force the window size.
            $browser->resizeWindow(1920, 1080);
        }

        $browser->on('before action', function ($action) use ($browser) {
            if ($this->recordScreenshots) {
                $timestamp = date('Y-m-d h\hi\ms\s');
                $filename = $this->prepareFileStorage('screenshots/' . "{$timestamp} about to $action");
                $screenshot = $browser->takeScreenshot($filename);
                $this->addFileArtefact($screenshot, [
                    'role' => 'screenshot'
                ]);
            }
        });

        $browser->on('after action', function ($action) use ($browser)  {
            if ($this->recordScreenshots) {
                $timestamp = date('Y-m-d h\hi\ms\s');
                $filename = $this->prepareFileStorage('screenshots/' . "{$timestamp} after $action");
                $screenshot = $browser->takeScreenshot($filename);
                $this->addFileArtefact($screenshot, [
                    'role' => 'screenshot'
                ]);
            }
        });
    }
}
