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

    }
}
