<?php

namespace PrestaShop\PSTest\Shop;

use Exception;

use PrestaShop\Selenium\SeleniumBrowserFactory;

use PrestaShop\PSTest\SystemSettings;
use PrestaShop\PSTest\LocalShopSourceSettings;

use PrestaShop\FileSystem\FileSystem;

class LocalShopFactory
{
    private $systemSettings;
    private $sourceSettings;
    private $browserFactory;

    private $fs; // helper

    public function __construct(
        SeleniumBrowserFactory $browserFactory,
        SystemSettings $systemSettings,
        LocalShopSourceSettings $sourceSettings
    )
    {
        $this->browserFactory = $browserFactory;
        $this->systemSettings = $systemSettings;
        $this->sourceSettings = $sourceSettings;

        $this->fs = new FileSystem();
    }

    public function makeShop(array $options)
    {
        $options = array_merge([
            'temporary' => false
        ], $options);

        $targetRoot = $this->systemSettings->getWWWPath();

        if (!is_dir($targetRoot)) {
            throw new Exception(sprintf('WWW path (`%s`) is not a directory.', $targetRoot));
        }

        $sourcesPath = $this->sourceSettings->getPathToShopFiles();

        if (!is_dir($sourcesPath)) {
            throw new Exception(sprintf('Path to shop source files (`%s`) is not a directory.', $targetRoot));
        }

        $targetFolderName = basename($sourcesPath);

        $targetPath = $this->fs->join($targetRoot, $targetFolderName);

        if (!is_dir($targetPath) && !is_file($targetPath)) {
            $this->fs->cpr($sourcesPath, $targetPath);
        }

        $shopSourceSettings = clone $this->sourceSettings;
        $shopSourceSettings->setPathToShopFiles($targetPath);

        $shopSystemSettings = clone $this->systemSettings;

        $browser = $this->browserFactory->makeBrowser();

        $shop = new LocalShop(
            $browser,
            $shopSystemSettings,
            $shopSourceSettings
        );

        return $shop;
    }
}
