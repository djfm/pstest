<?php

namespace PrestaShop\TestRunner\Tests\Fixtures;

class SmokeTest
{
    public function contextProvider()
    {
        return [
            ['country' => 'fr', 'language' => 'fr'],
            ['country' => 'fr', 'language' => 'en'],
            ['country' => 'de', 'language' => 'de'],
            ['country' => 'de', 'language' => 'fr']
        ];
    }

    public function test_Installation()
    {
        $this->shop->get('installer')->install(
            $this->context('language'),
            $this->context('country')
        );
    }

    public function test_ICanLoginToTheBackOffice()
    {
        $this->shop->get('backOffice')->visit('AdminLogin')->login();
    }

    public function test_ICanValidateAnOrder()
    {
        $this->shop->get('backOfficeOrderManager')->validateById(5);
    }
}
