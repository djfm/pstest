<?php

namespace PrestaShop\PSTest\Tests;

use PHPUnit_Framework_TestCase;

use PrestaShop\PSTest\SystemSettings;
use PrestaShop\PSTest\LocalShopSourceSettings;

class ConfigurationTest extends PHPUnit_Framework_TestCase
{
    private function getSettingsPath()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'pstest.settings.json';
    }

    public function test_SystemSettings_Are_Loaded()
    {
        $settings = new SystemSettings();

        $settings->loadFile($this->getSettingsPath());

        $this->assertEquals('tester', $settings->getDatabaseUser());
        $this->assertEquals('http://fa.st/', $settings->getWWWBase());
    }

    public function test_LocalShopSourceSettings_Are_Loaded()
    {
        $settings = new LocalShopSourceSettings();

        $settings->loadFile($this->getSettingsPath());

        $this->assertEquals('/some/path', $settings->getPathToShopFiles());
        $this->assertEquals('install-devvv', $settings->getInstallerFolderName());
        $this->assertEquals('bo', $settings->getBackOfficeFolderName());
    }
}
