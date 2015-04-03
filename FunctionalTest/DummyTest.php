<?php

namespace PrestaShop\FunctionalTest;

use PrestaShop\PSTest\TestCase\TestCase;

class DummyTest extends TestCase
{
    public function cacheInitialState()
    {
        return [
            'installer' => [
                'install' => [[
                    'language' => 'en',
                    'country' => 'us']
                ]]
        ];
    }

    public function test_tax_is_created()
    {
        $id_tax = $this->shop
        ->get('back-office')
        ->login()
        ->get('taxes')
        ->createTax('hello', 20, true);

        $this->assertInternalType('int', $id_tax);
        $this->assertGreaterThan(0, $id_tax);
    }
}
