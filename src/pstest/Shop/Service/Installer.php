<?php

namespace PrestaShop\PSTest\Shop\Service;

use Exception;

use PrestaShop\PSTest\Shop\LocalShop;

use PrestaShop\PSTest\Shop\PageObject\Installer\ChooseYourLanguagePage;

class Installer
{
    private $shop;
    private $browser;

    public function __construct(LocalShop $shop)
    {
        $this->shop = $shop;
        $this->browser = $shop->getBrowser();
    }

    public function install()
    {
        $this->browser->visit(
            $this->shop->getInstallerURL()
        );

        $chLanguage = new ChooseYourLanguagePage(
            $this->browser
        );

        $licensePage = $chLanguage->nextStep();
        $licensePage->agreeToTermsAndConditions();

        $storeInformation = $licensePage->nextStep();
        $storeInformation
            ->setShopName('test')
            ->setCountry('us')
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setEmailAddress('pub@prestashop.com')
            ->setPassword('123456789')
        ;

        $systemConfiguration = $storeInformation->nextStep();
        $systemConfiguration
            ->setDatabaseServerAddress('127.0.0.1:3307')
            ->setDatabaseName('PrestaShop')
            ->setDatabaseLogin('root')
            ->setDatabasePassword('')
            ->setDatabaseTablesPrefix('ps_')
        ;

        if (!$systemConfiguration->testDatabaseConnection()) {
            if (!$systemConfiguration->attemptToCreateDatabase()) {
                throw new Exception('Could not create database.');
            }
        }

        $systemConfiguration->nextStep();
    }
}
