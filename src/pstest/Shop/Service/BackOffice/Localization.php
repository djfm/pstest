<?php

namespace PrestaShop\PSTest\Shop\Service\BackOffice;

use PrestaShop\PSTest\Shop\Service\BackOffice\Service as BackOfficeService;
use PrestaShop\PSTest\Shop\Entity\Country;

class Localization extends BackOfficeService
{
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
