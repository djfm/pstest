<?php

namespace PrestaShop\FunctionalTest;

use PrestaShop\PSTest\TestCase\TestCase;

use PrestaShop\PSTest\Shop\Entity\Carrier;
use PrestaShop\PSTest\Shop\Entity\TaxRulesGroup;

class CarriersSetupTest extends TestCase
{
    private $backOffice;

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

    /**
     * @beforeClass
     */
    public function loginToTheBackOffice()
    {
        $this->backOffice = $this->shop->get('back-office')->login();
    }

    public function test_create_carrier()
    {
        $carrier = new Carrier;

        $carrier
        ->setName('Oh My Carrier')
        ->setTransitTime('28 days')
        ->setSpeedGrade(0)
        ->setAddHandlingCosts(true)
        ->setFreeShipping(false)
        ->setBillAccordingToPrice(true)
        ->setTaxRulesGroup((new TaxRulesGroup)->setId(1))
        ->setOutOfRangeBehavior(Carrier::BEHAVIOR_DISABLE)
        ;

        $this->backOffice->get('carriers')->createCarrier($carrier);
    }
}
