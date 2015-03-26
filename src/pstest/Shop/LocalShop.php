<?php

namespace PrestaShop\PSTest\Shop;

use Exception;

use PrestaShop\Selenium\Browser\BrowserInterface;

use PrestaShop\PSTest\Shop\Service\Installer;

use PrestaShop\PSTest\SystemSettings;
use PrestaShop\PSTest\LocalShopSourceSettings;

class LocalShop extends Shop implements ShopInterface
{
    private $systemSettings;
    private $sourceSettings;
    private $browser;

    public function __construct(
        BrowserInterface $browser,
        SystemSettings $systemSettings,
        LocalShopSourceSettings $sourceSettings
    )
    {
        parent::__construct();

        $this->browser        = $browser;
        $this->systemSettings = $systemSettings;
        $this->sourceSettings = $sourceSettings;

        $this->registerServices();
    }

    public function getInstallerURL()
    {
        return implode('/', [
            rtrim($this->systemSettings->getWWWBase(), '/'),
            basename($this->sourceSettings->getPathToShopFiles()),
            $this->sourceSettings->getInstallerFolderName()
        ]);
    }

    public function getBrowser()
    {
        return $this->browser;
    }

    public function getSystemSettings()
    {
        return $this->systemSettings;
    }

    public function registerServices()
    {
        $this->getContainer()->bind('installer', function () {
            return new Installer($this);
        }, true);
    }
}
