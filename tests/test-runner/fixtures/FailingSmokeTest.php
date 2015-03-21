<?php

namespace PrestaShop\TestRunner\Tests\Fixtures;

use Exception;

use PrestaShop\TestRunner\TestCase\TestCase;

class FailingSmokeTest extends TestCase
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

    /**
     * @beforeClass
     */
    public function someOtherInitialization()
    {

    }

    protected function test_Installation()
    {
    }

    protected function test_ICanLoginToTheBackOffice()
    {
        throw new Exception;
    }

    public function test_ICanValidateAnOrder()
    {
    }

    /**
     * @afterClass
     */
    public function someOtherTeardown()
    {

    }
}
