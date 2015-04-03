<?php

namespace PrestaShop\PSTest\Shop\Service;

use Exception;

use PrestaShop\PSTest\Shop\LocalShop;

class Installer
{
    private $shop;
    private $browser;

    public function __construct(LocalShop $shop)
    {
        $this->shop = $shop;
        $this->browser = $shop->getBrowser();
    }

    /**
     * Install the shop.
     *
     * @param  array  $shop_options     language (2-letter code), country (2-letter code), name
     * @param  array  $employee_options firstname, lastname, email, password
     */
    public function install(array $shop_options = array(), array $employee_options = array())
    {
        $shop_options = array_merge($this->shop->getDefaults('shop'), $shop_options);
        $employee_options = array_merge($this->shop->getDefaults('employee'), $employee_options);

        $this->browser->visit(
            $this->shop->getInstallerURL()
        );

        $chLanguage = $this->shop->getContainer()->make(
            'PrestaShop\PSTest\Shop\PageObject\Installer\ChooseYourLanguagePage'
        );

        $chLanguage->setLanguage($shop_options['language']);

        $licensePage = $chLanguage->nextStep();
        $licensePage->agreeToTermsAndConditions();

        $storeInformation = $licensePage->nextStep();
        $storeInformation
            ->setShopName($shop_options['name'])
            ->setCountry($shop_options['country'])
            ->setFirstName($employee_options['firstname'])
            ->setLastName($employee_options['lastname'])
            ->setEmailAddress($employee_options['email'])
            ->setPassword($employee_options['password'])
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

        return $this->shop;
    }
}
