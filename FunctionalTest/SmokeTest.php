<?php

namespace PrestaShop\PrestaShop\FunctionalTest;

use PrestaShop\PSTest\TestCase\PrestaShopTest;

class SmokeTest extends PrestaShopTest
{
    public function contextProvider()
    {
        return [
            ['country' => 'fr', 'language' => 'fr'],
            ['country' => 'fr', 'language' => 'de']
        ];
    }

    public function testInstallation()
    {
        $this->shop->get('installer')->install([
            'language' => $this->context('language'),
            'country' => $this->context('country')
        ]);
    }
}
