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
    }

    public function test_ICanLoginToTheBackOffice()
    {
    }

    public function test_ICanValidateAnOrder()
    {
    }
}
