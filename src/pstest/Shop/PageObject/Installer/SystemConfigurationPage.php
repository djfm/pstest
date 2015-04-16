<?php

namespace PrestaShop\PSTest\Shop\PageObject\Installer;

use PrestaShop\PSTest\Shop\Browser\Browser;
use PrestaShop\PSTest\Helper\SpinnerHelper as Spin;

class SystemConfigurationPage
{
    private $browser;

    public function __construct(Browser $browser)
    {
        $this->browser = $browser;
    }

    public function setDatabaseServerAddress($address)
    {
        $this->browser->fillIn('#dbServer', $address);

        return $this;
    }

    public function setDatabaseName($name)
    {
        $this->browser->fillIn('#dbName', $name);

        return $this;
    }

    public function setDatabaseLogin($login)
    {
        $this->browser->fillIn('#dbLogin', $login);

        return $this;
    }

    public function setDatabasePassword($password)
    {
        $this->browser->fillIn('#dbPassword', $password);

        return $this;
    }

    public function setDatabaseTablesPrefix($prefix)
    {
        $this->browser->fillIn('#db_prefix', $prefix);

        return $this;
    }

    public function testDatabaseConnection()
    {
        $this->browser->click('#btTestDB');

        return Spin::maybe(function () {
            return $this->browser->hasVisible('#dbResultCheck.okBlock');
        }, 5, 500);
    }

    public function attemptToCreateDatabase()
    {
        $this->browser->click('#btCreateDB');

        return $this->testDatabaseConnection();
    }

    public function nextStep()
    {
        $this->browser->click('#btNext');

        Spin::assertTrue(function () {
            return $this->browser->hasVisible('a.BO') && $this->browser->hasVisible('a.FO');
        }, 300, 1000, 'Installation did not complete successfully in the specified time.');

        return new InstallationFinishedPage($this->browser);
    }
}
