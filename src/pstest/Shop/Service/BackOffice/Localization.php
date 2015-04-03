<?php

namespace PrestaShop\PSTest\Shop\Service\BackOffice;

use PrestaShop\PSTest\Shop\Shop;
use PrestaShop\PSTest\Shop\Entity\Country;

class Localization
{
    private $shop;
    private $backOffice;
    private $browser;

    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
        $this->backOffice = $shop->get('back-office');
        $this->browser = $shop->getBrowser();
    }

    public function getCountryByISOCode($isoCode)
    {
        $this->backOffice->visitController('AdminCountries');

        $this->browser
        ->fillIn('input[name=countryFilter_iso_code]', $isoCode)
        ->click('#submitFilterButtoncountry');

        $id = (int)$this->browser->getText('table.country tr:first-of-type td:nth-of-type(2)');
        $name = $this->browser->getText('table.country tr:first-of-type td:nth-of-type(3)');

        $this->browser->clickButtonNamed('submitResetcountry');

        $country = (new Country)->setName($name)->setIsoCode($isoCode)->setId($id);

        return $country;
    }
}
