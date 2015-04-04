<?php

namespace PrestaShop\FunctionalTest;

use PrestaShop\PSTest\TestCase\TestCase;

use PrestaShop\PSTest\Shop\Entity\Carrier;
use PrestaShop\PSTest\Shop\Entity\CarrierZone;
use PrestaShop\PSTest\Shop\Entity\CarrierRange;
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

        $lowRange = (new CarrierRange)->setCost(10)->setFromIncluded(0)->setToExcluded(100);
        $midRange = (new CarrierRange)->setCost(20)->setFromIncluded(100)->setToExcluded(200);
        $topRange = (new CarrierRange)->setCost(30)->setFromIncluded(200)->setToExcluded(300);

        $carrierZone = (new CarrierZone)->setCost(50)->setId(1);
        $midRange->addZone($carrierZone);

        $carrier
        ->setName('Oh My Carrier')
        ->setTransitTime('28 days')
        ->setSpeedGrade(0)
        ->setAddHandlingCosts(true)
        ->setFreeShipping(false)
        ->setBillAccordingToPrice(true)
        ->setTaxRulesGroup((new TaxRulesGroup)->setId(1))
        ->setOutOfRangeBehavior(Carrier::BEHAVIOR_DISABLE)
        ->addRange($lowRange)->addRange($midRange)->addRange($topRange)
        ;

        $this->backOffice->get('carriers')->createCarrier($carrier);
    }
}
