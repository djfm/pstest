<?php

namespace PrestaShop\FunctionalTest;

use PrestaShop\PSTest\TestCase\TestCase;

class SmokeTest extends TestCase
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
        $this->shop->get('installer')->install();
    }
}
