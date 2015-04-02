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

    public function testSomething()
    {
        $this->shop
        ->get('back-office')
        ->login()
        ->get('taxes')
        ->createTax('hello', 20, true);        
        //$this->shop->getBrowser()->visit($this->shop->getFrontOfficeURL());
        sleep(2);
    }
}
