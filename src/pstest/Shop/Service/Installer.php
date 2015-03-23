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

        $systemSettings = $this->shop->getSystemSettings();
        $systemConfiguration = $storeInformation->nextStep();
        $systemConfiguration
            ->setDatabaseServerAddress(
                $systemSettings->getDatabaseHostAndPort()
            )
            ->setDatabaseName(
                $systemSettings->getDatabaseName()
            )
            ->setDatabaseLogin(
                $systemSettings->getDatabaseUser()
            )
            ->setDatabasePassword(
                $systemSettings->getDatabasePass()
            )
            ->setDatabaseTablesPrefix(
                $systemSettings->getDatabaseTablesPrefix()
            )
        ;

        if (!$systemConfiguration->testDatabaseConnection()) {
            if (!$systemConfiguration->attemptToCreateDatabase()) {
                throw new Exception('Could not create database.');
            }
        }

        $systemConfiguration->nextStep();
    }
}
