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

    private $shopCache = null;

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

    private function getUID($folder)
    {
        $uid_lock_path = $this->fs->join($folder, 'pstest.maxuid.lock');
        $h = fopen($uid_lock_path, 'c+');
        if (!$h) {
            throw new \Exception('Could not get pstaf.maxuid.lock file.');
        }
        flock($h, LOCK_EX);
        $uid = (int) fgets($h) + 1;
        ftruncate($h, 0);
        rewind($h);
        fwrite($h, "$uid");
        fflush($h);
        flock($h, LOCK_UN);
        fclose($h);
        return $uid;
    }

    public function makeShop(array $options = array())
    {
        $options = array_merge([
            'temporary' => false
        ], $options);

        $targetRoot = $this->systemSettings->getWWWPath();

        if (!is_dir($targetRoot)) {
            throw new Exception(sprintf('WWW path (`%s`) is not a directory.', $targetRoot));
        }

        if ($this->shopCache) {
            $sourcesPath = $this->fs->join($this->shopCache, 'files');
        } else {
            $sourcesPath = $this->sourceSettings->getPathToShopFiles();
        }

        if (!is_dir($sourcesPath)) {
            throw new Exception(sprintf('Path to shop source files (`%s`) is not a directory.', $targetRoot));
        }

        $marker = '_tmpshpcpy_';
        $uid = $this->getUID($targetRoot);

        if ($options['temporary']) {
            $suffix = $marker . $uid;
        } else {
            $suffix = '';
        }

        $targetFolderName = basename($sourcesPath) . $suffix;

        if ($this->shopCache) {
            // Try to apply the suffix to the old name of the shop
            $infoFile = $this->fs->join($this->shopCache, 'info.json');
            if (file_exists($infoFile)) {
                $info = json_decode(file_get_contents($infoFile), true);
                $oldName = $info['name'];
                if (false !== ($pos = strpos($oldName, $marker))) {
                    $targetFolderName = substr($oldName, 0, $pos) . $suffix;
                }
            }
        }

        $targetPath = $this->fs->join($targetRoot, $targetFolderName);

        if (!is_dir($targetPath) && !is_file($targetPath)) {
            $this->fs->cpr($sourcesPath, $targetPath);
        }

        $shopSourceSettings = clone $this->sourceSettings;
        $shopSourceSettings->setPathToShopFiles($targetPath);

        $shopSystemSettings = clone $this->systemSettings;
        $shopSystemSettings->setDatabaseName(
            $shopSystemSettings->getDatabaseName() . $suffix
        );

        $browser = $this->browserFactory->makeBrowser();

        $browser->setScriptTimeout(15);

        $shop = new LocalShop(
            $browser,
            $shopSystemSettings,
            $shopSourceSettings
        );

        if ($this->shopCache) {
            $shop->get('database')->load(
                $this->fs->join($this->shopCache, 'database.sql')
            );

            $this->updateShopAfterMove($shop);
        }

        return $shop;
    }

    private function updateShopAfterMove(LocalShop $shop)
    {
        $targetPath = $shop->getSourceSettings()->getPathToShopFiles();
        $shopSystemSettings = $shop->getSystemSettings();
        $targetFolderName = basename($targetPath);

        // Set new database name in settings.inc.php
        $settingsFile = $this->fs->join($targetPath, 'config', 'settings.inc.php');
        if (file_exists($settingsFile)) {
            $settings = file_get_contents($settingsFile);
            $newSettings = preg_replace(
                '/(define\s*\(\s*([\'"])_DB_NAME_\2\s*,\s*([\'"]))(.*?)((\3)\s*\)\s*;)/',
                '${1}' . $shopSystemSettings->getDatabaseName() . '${5}',
                $settings
            );
            file_put_contents($settingsFile, $newSettings);
        }

        // Update the physical_uri stored in the restored shop's database
        $shop->get('database')->changePhysicalURI($targetFolderName);

        // Update the .htaccess
        $htaccessFile = $this->fs->join($targetPath, '.htaccess');
        if (file_exists($htaccessFile)) {
            $uri = '/' . $targetFolderName . '/';
            $htaccess = file_get_contents($htaccessFile);
            $rewrite_exp = '/(^\s*RewriteRule\s+\.\s+-\s+\[\s*E\s*=\s*REWRITEBASE\s*:)\/[^\/]+\/([^\]]*\]\s*$)/mi';
            $htaccess = preg_replace($rewrite_exp, '${1}'.$uri.'${2}', $htaccess);
            $errdoc_exp = '/(^\s*ErrorDocument\s+\w+\s+)\/[^\/]+\/(.*?$)/mi';
            $htaccess = preg_replace($errdoc_exp, '${1}'.$uri.'${2}', $htaccess);
            file_put_contents($htaccessFile, $htaccess);
        }
    }

    public function setShopCache($dirname)
    {
        $this->shopCache = $dirname;
        return $this;
    }

    public function cacheShop(LocalShop $shop, $dirname)
    {
        $this->fs->cpr(
            $shop->getSourceSettings()->getPathToShopFiles(),
            $this->fs->join($dirname, 'files')
        );

        $shop->get('database')->dump(
            $this->fs->join($dirname, 'database.sql')
        );

        $info = [
            'name' => basename($shop->getSourceSettings()->getPathToShopFiles())
        ];

        file_put_contents(
            $this->fs->join($dirname, 'info.json'),
            json_encode($info, JSON_PRETTY_PRINT)
        );

        return $this;
    }
}
