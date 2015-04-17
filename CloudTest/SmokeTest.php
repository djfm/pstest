<?php

namespace PrestaShop\PrestaShop\CloudTest;

use PrestaShop\PSTest\TestCase\CloudTest;
use PrestaShop\PSTest\Shop\RemoteShop;
use PrestaShop\PSTest\Helper\SpinnerHelper as Spin;

class SmokeTest extends CloudTest
{
    private $shop;

    public function contextProvider()
    {
        return [[
            'onboardingLanguage' => 'fr',
            'shopCountry' => 'France'
        ]];
    }

    /**
     * @beforeClass
     */
    public function prepareShop()
    {
        $this->shop = new RemoteShop;
        $this->shop->setName($this->customer->getUID());
    }

    public function test_account_and_shop_are_created()
    {
        $this->application->createAccountAndShop(
            $this->customer,
            $this->context('onboardingLanguage'),
            $this->context('shopCountry'),
            $this->shop
        );
    }

    public function test_shop_front_office_becomes_available_without_redirection()
    {
        Spin::assertTrue(function () {
            $ch = curl_init($this->shop->getFrontOfficeURL());
			curl_setopt($ch, CURLOPT_NOBODY, true);
			curl_exec($ch);
			$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			return $status == 200;
        }, 3600, 1000, 'Did not get final FO URL in 1 hour!');
    }
}
