<?php

namespace PrestaShop\PSTest\Shop;

use Exception;

use PrestaShop\Selenium\Browser\BrowserInterface;

use PrestaShop\PSTest\Shop\Service\Installer;

use PrestaShop\PSTest\Helper\MySQL as DatabaseHelper;
use PrestaShop\PSTest\Shop\Service\Database as DatabaseService;

use PrestaShop\PSTest\SystemSettings;
use PrestaShop\PSTest\LocalShopSourceSettings;

use PrestaShop\FileSystem\FileSystemOverHTTP;
use PrestaShop\PSTest\Shop\Service\Files as FilesService;

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

    public function getFrontOfficeURL()
    {
        return implode('/', [
            rtrim($this->systemSettings->getWWWBase(), '/'),
            basename($this->sourceSettings->getPathToShopFiles())
        ]);
    }

    public function getBackOfficeURL()
    {
        return $this->getFrontOfficeURL() . '/' . $this->sourceSettings->getBackOfficeFolderName();
    }

    public function getInstallerURL()
    {
        return $this->getFrontOfficeURL() . '/' . $this->sourceSettings->getInstallerFolderName();
    }

    public function getBrowser()
    {
        return $this->browser;
    }

    public function getSystemSettings()
    {
        return $this->systemSettings;
    }

    public function getSourceSettings()
    {
        return $this->sourceSettings;
    }

    public function registerServices()
    {
        $this->getContainer()->bind('installer', function () {

            return new Installer($this);

        }, true);

        $this->getContainer()->bind('database', function () {

            $db = new DatabaseHelper(
                $this->getSystemSettings()->getDatabaseHost(),
                $this->getSystemSettings()->getDatabasePort(),
                $this->getSystemSettings()->getDatabaseUser(),
                $this->getSystemSettings()->getDatabasePass()
            );

            return new DatabaseService($this, $db);
        }, true);

        $this->getContainer()->bind('files', function () {

            $fs = new FileSystemOverHTTP(
                $this->getSourceSettings()->getPathToShopFiles(),
                $this->getFrontOfficeURL()
            );

            return new FilesService($this, $fs);
        }, true);

        parent::registerServices();

        return $this;
    }
}
