<?php

namespace PrestaShop\PSTest\TestCase;

use PrestaShop\PSTest\RunnerPlugin\ConfigReader as ConfigReaderPlugin;
use PrestaShop\PSTest\RunnerPlugin\PrestaShopTest as PrestaShopTestPlugin;

use PrestaShop\PSTest\SystemSettings;
use PrestaShop\PSTest\LocalShopSourceSettings;

use PrestaShop\Selenium\Browser\BrowserInterface;

use PrestaShop\PSTest\Shop\LocalShopFactory;
use PrestaShop\PSTest\Shop\DefaultSettings;
use PrestaShop\FileSystem\FileSystemHelper as FS;

use Exception;

abstract class PrestaShopTest extends SeleniumEnabledTest
{
    protected $shop;

    private $shopIsTemporary = true;

    private $systemSettings;
    private $sourceSettings;
    private $defaultSettings;

    private $prestaShopTestPluginOptions = [];

    public function getRunnerPlugins()
    {
        $plugins = parent::getRunnerPlugins();

        $plugins['PrestaShopTest'] = new PrestaShopTestPlugin;
        $plugins['config'] = new ConfigReaderPlugin;

        return $plugins;
    }

    public function setRunnerPluginData($pluginName, $pluginData)
    {
        parent::setRunnerPluginData($pluginName, $pluginData);

        if ($pluginName === 'config') {
            $this->systemSettings  = $pluginData['systemSettings'];
            $this->sourceSettings  = $pluginData['sourceSettings'];
            $this->defaultSettings = $pluginData['defaultSettings'];
        } else if ($pluginName === 'PrestaShopTest') {
            $this->prestaShopTestPluginOptions = $pluginData;
        }
    }

    /**
     * Return a description of how to initialize the shop.
     *
     * Should be an associative array like this:
     * [
     * 		'some.service' => [
     * 			'some_method' => [some, arguments]
     * 		],
     * 		'some.other.service' => [
     * 			'some_method_2' => [true, false],
     * 			'some_method_3' => [42],
     * 		]
     * ]
     *
     * The methods on services will be called in order, with the provided arguments.
     * Services are anything that the shop can return via a call to `get`.
     *
     * The description of the initial state is done as a plain array like this so that shops may be put in
     * cache.
     */
    public function cacheInitialState()
    {
        return [];
    }

    private function getCacheDir()
    {
        return 'pstest-cache';
    }

    final public function setupBeforeClass()
    {
        $cacheInitialState = $this->cacheInitialState();
        $initialStateCacheKey = md5(serialize($cacheInitialState));
        $initialStateLockFile = null;
        $lock = null;
        $useCache = false;

        if (!empty($cacheInitialState)) {
            $useCache = true;
            $initialStateLockFile = FS::join($this->getCacheDir(), 'initialState_' . $initialStateCacheKey . '.lock');

            if (!is_dir(dirname($initialStateLockFile))) {
                if (!@mkdir(dirname($initialStateLockFile), 0777, true)) {
                    throw new Exception(
                        sprintf('Could not create directory `%s`.', dirname($initialStateLockFile))
                    );
                }
            }

            $lock = fopen($initialStateLockFile, 'c+');
            if (!$lock) {
                throw new Exception(
                    sprintf('Could not create lock file `%s`.', $initialStateLockFile)
                );
            }
            flock($lock, LOCK_EX);
        }

        $shopFactory = new LocalShopFactory($this->browserFactory, $this->systemSettings, $this->sourceSettings);

        $cacheIsWarm = false;
        if ($useCache) {
            $cachedShopDir = FS::join($this->getCacheDir(), 'initialState_' . $initialStateCacheKey);

            if (is_dir($cachedShopDir)) {
                $cacheIsWarm = true;
                $shopFactory->setShopCache($cachedShopDir);
            }
        }

        $this->shop = $shopFactory->makeShop([
            'temporary' => $this->shopIsTemporary
        ]);

        $this->shop->setDefaults($this->defaultSettings);

        if ($useCache && !$cacheIsWarm) {
            foreach ($cacheInitialState as $serviceName => $calls) {
                $service = $this->shop->get($serviceName);
                foreach ($calls as $method => $arguments) {
                    call_user_func_array([$service, $method], $arguments);
                }
            }
            $shopFactory->cacheShop($this->shop, $cachedShopDir);
        }

        $this->setupBrowser($this->shop->getBrowser());

        if ($lock) {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    private function setupBrowser(BrowserInterface $browser)
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

    public function tearDownAfterClass()
    {
        if ($this->shopIsTemporary) {
            if (empty($this->prestaShopTestPluginOptions['no-cleanup'])) {
                $this->shop->get('database')->drop();
                $this->shop->get('files')->removeAll();
            }
        }
    }
}
