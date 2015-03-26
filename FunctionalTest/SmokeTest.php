<?php

namespace PrestaShop\FunctionalTest;

use PrestaShop\PSTest\TestCase\TestCase;

class SmokeTest extends TestCase
{
    public function contextProvider()
    {
        return [
            ['country' => 'fr', 'language' => 'fr'],
        ];
    }

    public function testInstallation()
    {
        
    }
}
